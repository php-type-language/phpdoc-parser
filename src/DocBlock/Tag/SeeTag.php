<?php

declare(strict_types=1);

namespace TypeLang\PhpDocParser\DocBlock\Tag;

use TypeLang\PhpDocParser\DocBlock\Reference\ReferenceInterface;

/**
 * @link https://docs.phpdoc.org/3.0/guide/references/phpdoc/tags/see.html#see
 */
final class SeeTag extends Tag
{
    public function __construct(
        private readonly ReferenceInterface $reference,
        \Stringable|string|null $description = null,
    ) {
        parent::__construct('see', $description);
    }

    public function getReference(): ReferenceInterface
    {
        return $this->reference;
    }
}
