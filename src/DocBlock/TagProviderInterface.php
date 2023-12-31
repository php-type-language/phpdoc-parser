<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\DocBlock;

use TypeLang\PhpDoc\Parser\DocBlock\Tag\TagInterface;

/**
 * @template-extends \IteratorAggregate<array-key, TagInterface>
 */
interface TagProviderInterface extends \IteratorAggregate, \Countable
{
    /**
     * Returns the tags for this DocBlock.
     *
     * @psalm-immutable
     * @return list<TagInterface>
     */
    public function getTags(): iterable;

    /**
     * @psalm-immutable
     * @return int<0, max>
     */
    public function count(): int;
}
