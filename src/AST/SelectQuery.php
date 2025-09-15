<?php

declare(strict_types=1);

namespace QueryLang\AST;

/**
 * AST node representing a SELECT query
 */
class SelectQuery extends Node
{
    public function __construct(
        public array $columns,
        public string $from,
        public ?WhereClause $where = null,
        public ?int $limit = null
    ) {}

    public function getType(): string
    {
        return 'SELECT';
    }
}