<?php

declare(strict_types=1);

namespace TypeLang\PhpDocParser\DocBlock\Tag;

use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\PhpDocParser\DocBlock\Description;

/**
 * TODO Add support of property name parsing: {@link https://docs.phpdoc.org/3.0/guide/references/phpdoc/tags/param.html#param}
 */
final class ParamTag extends TypedTag
{
    public function __construct(TypeStatement $type, Description|string|null $description = null)
    {
        parent::__construct('param', $type, $description);
    }
}
