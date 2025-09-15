<?php

declare(strict_types=1);

namespace QueryLang;

use QueryLang\AST\SelectQuery;
use QueryLang\AST\Condition;

/**
 * Compiler that converts AST to executable PHP operations
 */
class Compiler
{
    /**
     * Compile an AST to executable operations
     *
     * @param SelectQuery $ast The AST to compile
     * @return array Compiled operations
     */
    public function compile(SelectQuery $ast): array
    {
        $operations = [
            'type' => 'SELECT',
            'from' => $ast->from,
            'columns' => $ast->columns,
        ];

        if ($ast->where) {
            $operations['where'] = $this->compileCondition($ast->where->condition);
        }

        if ($ast->limit !== null) {
            $operations['limit'] = $ast->limit;
        }

        return $operations;
    }

    /**
     * Compile a condition to executable operations
     *
     * @param Condition $condition The condition to compile
     * @return array Compiled condition operations
     */
    private function compileCondition(Condition $condition): array
    {
        if ($condition->isSimple()) {
            return [
                'type' => 'simple',
                'column' => $condition->column,
                'operator' => $condition->operator,
                'value' => $condition->value,
            ];
        }

        if ($condition->isCompound()) {
            return [
                'type' => 'compound',
                'logical_operator' => strtoupper($condition->logicalOperator),
                'left' => $this->compileCondition($condition->leftCondition),
                'right' => $this->compileCondition($condition->rightCondition),
            ];
        }

        throw new QueryException("Invalid condition structure");
    }
}