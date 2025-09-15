<?php

declare(strict_types=1);

namespace QueryLang;

/**
 * Executor that runs compiled operations against PHP arrays
 */
class Executor
{
    /**
     * Execute compiled operations against variables
     *
     * @param array $operations The compiled operations
     * @param array $variables The variables context
     * @return array The execution result
     * @throws QueryException
     */
    public function execute(array $operations, array $variables): array
    {
        if ($operations['type'] !== 'SELECT') {
            throw new QueryException("Only SELECT operations are supported");
        }

        // Get the source array
        $sourceArray = $this->getSourceArray($operations['from'], $variables);
        
        // Apply WHERE filter if present
        if (isset($operations['where'])) {
            $sourceArray = $this->applyWhere($sourceArray, $operations['where']);
        }

        // Apply column selection
        $result = $this->selectColumns($sourceArray, $operations['columns']);

        // Apply LIMIT if present
        if (isset($operations['limit'])) {
            $result = array_slice($result, 0, $operations['limit']);
        }

        return $result;
    }

    /**
     * Get the source array from variables
     *
     * @param string $from The FROM clause (variable name)
     * @param array $variables The variables context
     * @return array The source array
     * @throws QueryException
     */
    private function getSourceArray(string $from, array $variables): array
    {
        // Remove $ prefix if present
        $varName = ltrim($from, '$');
        
        if (!isset($variables[$varName])) {
            throw new QueryException("Variable '{$varName}' not found in context");
        }

        $source = $variables[$varName];
        
        if (!is_array($source)) {
            throw new QueryException("Variable '{$varName}' is not an array");
        }

        return $source;
    }

    /**
     * Apply WHERE clause filtering
     *
     * @param array $data The data to filter
     * @param array $where The WHERE operations
     * @return array The filtered data
     */
    private function applyWhere(array $data, array $where): array
    {
        return array_filter($data, function ($row) use ($where) {
            return $this->evaluateCondition($row, $where);
        });
    }

    /**
     * Evaluate a condition against a row
     *
     * @param array $row The data row
     * @param array $condition The condition to evaluate
     * @return bool Whether the condition matches
     */
    private function evaluateCondition(array $row, array $condition): bool
    {
        if ($condition['type'] === 'simple') {
            return $this->evaluateSimpleCondition($row, $condition);
        }

        if ($condition['type'] === 'compound') {
            $leftResult = $this->evaluateCondition($row, $condition['left']);
            $rightResult = $this->evaluateCondition($row, $condition['right']);
            
            return match ($condition['logical_operator']) {
                'AND' => $leftResult && $rightResult,
                'OR' => $leftResult || $rightResult,
                default => throw new QueryException("Unknown logical operator: " . $condition['logical_operator'])
            };
        }

        throw new QueryException("Unknown condition type: " . $condition['type']);
    }

    /**
     * Evaluate a simple condition
     *
     * @param array $row The data row
     * @param array $condition The simple condition
     * @return bool Whether the condition matches
     */
    private function evaluateSimpleCondition(array $row, array $condition): bool
    {
        $column = $condition['column'];
        $operator = $condition['operator'];
        $expectedValue = $condition['value'];

        if (!array_key_exists($column, $row)) {
            return false;
        }

        $actualValue = $row[$column];

        return match ($operator) {
            '=' => $actualValue == $expectedValue,
            '!=' => $actualValue != $expectedValue,
            '<' => $actualValue < $expectedValue,
            '>' => $actualValue > $expectedValue,
            '<=' => $actualValue <= $expectedValue,
            '>=' => $actualValue >= $expectedValue,
            default => throw new QueryException("Unknown operator: " . $operator)
        };
    }

    /**
     * Select columns from the data
     *
     * @param array $data The data
     * @param array $columns The columns to select
     * @return array The selected data
     */
    private function selectColumns(array $data, array $columns): array
    {
        if (in_array('*', $columns)) {
            return $data;
        }

        return array_map(function ($row) use ($columns) {
            $result = [];
            foreach ($columns as $column) {
                if (array_key_exists($column, $row)) {
                    $result[$column] = $row[$column];
                }
            }
            return $result;
        }, $data);
    }
}