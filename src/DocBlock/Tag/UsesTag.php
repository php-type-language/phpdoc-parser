<?php

declare(strict_types=1);

namespace TypeLang\PhpDocParser\DocBlock\Tag;

use TypeLang\PhpDocParser\DocBlock\Description;

/**
 * TODO Add fqsen extraction: {@link https://docs.phpdoc.org/3.0/guide/references/phpdoc/tags/uses.html#uses-used-by}
 */
final class UsesTag extends Tag
{
    public function __construct(Description|string|null $description = null)
    {
        parent::__construct('uses', $description);
    }
}
