<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Tag;

/**
 * @mixin TagProviderInterface
 * @mixin \IteratorAggregate
 *
 * @psalm-require-implements TagProviderInterface
 * @psalm-require-implements \IteratorAggregate
 *
 * @internal This is an internal library trait, please do not use it in your code.
 * @psalm-internal TypeLang\PHPDoc\Tag
 */
trait TagProvider
{
    /**
     * @var list<TagInterface>
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected readonly array $tags;

    /**
     * @param iterable<array-key, TagInterface> $tags
     * @psalm-suppress InaccessibleProperty
     */
    protected function bootTagProvider(iterable $tags): void
    {
        $this->tags = \array_values([...$tags]);
    }

    /**
     * {@see TagProviderInterface::getTags()}
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return \Traversable<int<0, max>, TagInterface>
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->tags as $tag) {
            yield $tag;

            $description = $tag->getDescription();

            if ($description instanceof TagProviderInterface) {
                yield from $description;
            }
        }
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->tags);
    }
}
