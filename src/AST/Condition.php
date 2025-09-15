<?php

declare(strict_types=1);

namespace QueryLang\AST;

/**
 * AST node representing a condition (can be simple or compound)
 */
class Condition extends Node
{
    public function __construct(
        public ?string $column = null,
        public ?string $operator = null,
        public mixed $value = null,
        public ?string $logicalOperator = null,
        public ?Condition $leftCondition = null,
        public ?Condition $rightCondition = null
    ) {}

    public function getType(): string
    {
        return 'CONDITION';
    }

    /**
     * Check if this is a simple condition (column operator value)
     */
    public function isSimple(): bool
    {
        return $this->column !== null && $this->operator !== null && $this->value !== null;
    }

    /**
     * Check if this is a compound condition (left AND/OR right)
     */
    public function isCompound(): bool
    {
        return $this->logicalOperator !== null && $this->leftCondition !== null && $this->rightCondition !== null;
    }
}