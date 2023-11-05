<?php

declare(strict_types=1);

namespace TypeLang\PhpDocParser\DocBlock\Tag;

use TypeLang\PhpDocParser\DocBlock\Description;

/**
 * TODO Add version support: {@link https://docs.phpdoc.org/3.0/guide/references/phpdoc/tags/deprecated.html#deprecated}
 */
final class DeprecatedTag extends Tag
{
    public function __construct(Description|string|null $description = null)
    {
        parent::__construct('deprecated', $description);
    }
}
