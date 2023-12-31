<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\DocBlock\Tag;

use TypeLang\Parser\Node\Stmt\TypeStatement;

/**
 * This tag is created if a parsing error occurs while parsing the original
 * tag that contain {@see TypeStatement} reference.
 */
final class InvalidTypedTag extends TypedTag implements InvalidTagInterface
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        string $name,
        TypeStatement $type,
        private readonly \Throwable $reason,
        \Stringable|string|null $description = null
    ) {
        parent::__construct($name, $type, $description);
    }

    public function getReason(): \Throwable
    {
        return $this->reason;
    }

    /**
     * @return array{
     *     name: non-empty-string,
     *     error: non-empty-string,
     *     type: array{
     *         kind: int<0, max>,
     *         ...
     *     },
     *     description?: array{
     *         template: string,
     *         tags: list<array>
     *     }
     * }
     */
    public function toArray(): array
    {
        $reason = $this->getReason();

        return [
            ...parent::toArray(),
            'type' => $this->type->toArray(),
            'error' => $reason->getMessage(),
        ];
    }
}
