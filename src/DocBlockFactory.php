<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser;

use TypeLang\Parser\Parser;
use TypeLang\PhpDoc\Parser\Description\SprintfDescriptionFactory;
use TypeLang\PhpDoc\Parser\DocBlock\Description;
use TypeLang\PhpDoc\Parser\Description\DescriptionFactoryInterface;
use TypeLang\PhpDoc\Parser\DocBlock\Registry;
use TypeLang\PhpDoc\Parser\DocBlock\RegistryInterface;
use TypeLang\PhpDoc\Parser\DocBlock\Tag\TagInterface;
use TypeLang\PhpDoc\Parser\DocBlock\TagFactoryInterface;
use JetBrains\PhpStorm\Language;
use TypeLang\PhpDoc\Parser\Provider\StandardTagProvider;

/**
 * @psalm-suppress UndefinedAttributeClass : JetBrains language attribute may not be available
 */
class DocBlockFactory implements DocBlockFactoryInterface
{
    /**
     * @param TagFactoryInterface<TagInterface> $tags
     */
    final public function __construct(
        public readonly DescriptionFactoryInterface $descriptions,
        public readonly TagFactoryInterface $tags,
    ) {}

    protected static function createDescriptionFactory(TagFactoryInterface $tags): DescriptionFactoryInterface
    {
        return new SprintfDescriptionFactory($tags);
    }

    protected static function createRegistry(): RegistryInterface
    {
        return new Registry();
    }

    public static function createInstance(?Parser $parser = null): self
    {
        $descriptions = static::createDescriptionFactory(
            tags: $tags = static::createRegistry(),
        );

        $tags->setDescriptionFactory($descriptions);

        // Load standard tags
        $provider = new StandardTagProvider($parser ?? new Parser(true));

        foreach ($provider->getTags() as $tag => $factory) {
            if ($factory->getDescriptionFactory() === null) {
                $factory = $factory->withDescriptionFactory($descriptions);
            }

            $tags->add($factory, ...(array)$tag);
        }

        return new static($descriptions, $tags);
    }

    public function create(#[Language('PHP')] string $docblock): DocBlock
    {
        $docblock = $this->stripDocComment($docblock);
        $docblock = $this->normalizeLineDelimiters($docblock);

        [$description, $tags] = $this->splitByDocBlockTags($docblock);

        return new DocBlock($description, $tags);
    }

    /**
     * @return array{Description, list<TagInterface>}
     */
    private function splitByDocBlockTags(string $docblock): array
    {
        $description = '';
        $tags = [];

        foreach (\explode("\n@", $docblock) as $i => $tag) {
            if ($i === 0) {
                if (\str_starts_with($tag, '@')) {
                    $tags[] = $this->tags->create($tag);
                } else {
                    $description = $tag;
                }

                continue;
            }

            if (\str_starts_with($tag, ' ')) {
                $description .= "\n@{$tag}";
                continue;
            }

            $tags[] = $this->tags->create("@{$tag}");
        }

        return [$this->descriptions->create($description), $tags];
    }

    private function normalizeLineDelimiters(string $docblock): string
    {
        return \str_replace(["\r\n", "\r"], "\n", $docblock);
    }

    private function stripDocComment(string $docblock): string
    {
        if ($docblock === '') {
            return '';
        }

        $docblock = \preg_replace(
            pattern: '/[ \t]*(?:\/\*+|\*+\/|\*+)?[ \t]?(.*)?/u',
            replacement: '$1',
            subject: $docblock,
        )
            ?: $docblock;

        if (\str_ends_with($docblock, '*/')) {
            $docblock = \substr($docblock, 0, -2);
        }

        return \trim($docblock);
    }
}
