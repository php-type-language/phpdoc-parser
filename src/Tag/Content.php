<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Tag;

use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\Parser\ParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Exception\InvalidTagException;
use TypeLang\PHPDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PHPDoc\Tag\Content\OptionalTypeParserApplicator;
use TypeLang\PHPDoc\Tag\Content\ValueApplicator;
use TypeLang\PHPDoc\Tag\Content\OptionalValueApplicator;
use TypeLang\PHPDoc\Tag\Content\OptionalVariableNameApplicator;
use TypeLang\PHPDoc\Tag\Content\TypeParserApplicator;
use TypeLang\PHPDoc\Tag\Content\VariableNameApplicator;

class Content implements \Stringable
{
    private readonly string $original;

    /**
     * @var int<0, max>
     * @psalm-readonly-allow-private-mutation
     */
    public int $offset = 0;

    public function __construct(
        public string $value,
    ) {
        $this->original = $this->value;
    }

    /**
     * @param int<0, max> $offset
     */
    public function shift(int $offset, bool $ltrim = true): void
    {
        if ($offset <= 0) {
            return;
        }

        $size = \strlen($this->value);
        $this->value = \substr($this->value, $offset);

        if ($ltrim) {
            $this->value = \ltrim($this->value);
        }

        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $this->offset += $size - \strlen($this->value);
    }

    public function getTagException(string $message, \Throwable $previous = null): InvalidTagException
    {
        return new InvalidTagException(
            source: $this->original,
            offset: $this->offset,
            message: $message,
            previous: $previous,
        );
    }

    /**
     * @api
     * @param non-empty-string $tag
     */
    public function nextType(string $tag, TypesParserInterface $parser): TypeStatement
    {
        return $this->apply(new TypeParserApplicator($tag, $parser));
    }

    /**
     * @api
     */
    public function nextOptionalType(TypesParserInterface $parser): ?TypeStatement
    {
        return $this->apply(new OptionalTypeParserApplicator($parser));
    }

    /**
     * @api
     * @param non-empty-string $tag
     * @return non-empty-string
     */
    public function nextVariable(string $tag): string
    {
        return $this->apply(new VariableNameApplicator($tag));
    }

    /**
     * @api
     * @return non-empty-string|null
     */
    public function nextOptionalVariable(): ?string
    {
        return $this->apply(new OptionalVariableNameApplicator());
    }

    /**
     * @template T of non-empty-string
     *
     * @api
     * @param non-empty-string $tag
     * @param T $value
     * @return T
     */
    public function nextValue(string $tag, string $value): string
    {
        return $this->apply(new ValueApplicator($tag, $value));
    }

    /**
     * @template T of non-empty-string
     *
     * @api
     * @param non-empty-string $tag
     * @param T $value
     * @return T|null
     */
    public function nextOptionalValue(string $value): ?string
    {
        return $this->apply(new OptionalValueApplicator($value));
    }

    /**
     * @template T of mixed
     * @param callable(Content):T $applicator
     * @return T
     */
    public function apply(callable $applicator): mixed
    {
        return $applicator($this);
    }

    public function toDescription(DescriptionParserInterface $descriptions): DescriptionInterface
    {
        return $descriptions->parse($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
