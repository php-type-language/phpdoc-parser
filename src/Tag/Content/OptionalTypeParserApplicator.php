<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Tag\Content;

use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\Parser\ParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Exception\InvalidTagException;
use TypeLang\PHPDoc\Tag\Content;

/**
 * @template-extends Applicator<TypeStatement|null>
 */
final class OptionalTypeParserApplicator extends Applicator
{
    /**
     * @param non-empty-string $tag
     */
    public function __construct(
        private readonly TypesParserInterface $parser,
    ) {}

    /**
     * {@inheritDoc}
     *
     * @throws \Throwable
     * @throws InvalidTagException
     */
    public function __invoke(Content $lexer): ?TypeStatement
    {
        try {
            $type = $this->parser->parse($lexer->value);
        } catch (ParserExceptionInterface $e) {
            return null;
        }

        /**
         * @psalm-suppress MixedArgument
         * @psalm-suppress NoInterfaceProperties
         */
        $lexer->shift($this->parser->lastProcessedTokenOffset);

        return $type;
    }
}
