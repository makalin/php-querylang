<?php

declare(strict_types=1);

namespace QueryLang\AST;

/**
 * AST node representing a WHERE clause
 */
class WhereClause extends Node
{
    public function __construct(
        public Condition $condition
    ) {}

    public function getType(): string
    {
        return 'WHERE';
    }
}