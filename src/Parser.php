<?php

declare(strict_types=1);

namespace QueryLang;

use QueryLang\AST\SelectQuery;
use QueryLang\AST\WhereClause;
use QueryLang\AST\Condition;

/**
 * SQL parser that converts query strings to AST
 */
class Parser
{
    /**
     * Parse a SQL-like query string into an AST
     *
     * @param string $query The query string to parse
     * @return SelectQuery The parsed AST
     * @throws QueryException
     */
    public function parse(string $query): SelectQuery
    {
        $query = trim($query);
        
        if (empty($query)) {
            throw new QueryException("Empty query provided");
        }

        // Parse SELECT statement
        if (!preg_match('/^SELECT\s+(.+?)\s+FROM\s+([^\s]+)(?:\s+(.+))?$/i', $query, $matches)) {
            throw new QueryException("Invalid SELECT query format");
        }

        $columns = $this->parseColumns(trim($matches[1]));
        $from = trim($matches[2]);
        $remaining = isset($matches[3]) ? trim($matches[3]) : '';

        $where = null;
        $limit = null;

        // Parse WHERE clause
        if (preg_match('/WHERE\s+(.+?)(?:\s+LIMIT\s+(\d+))?$/i', $remaining, $whereMatches)) {
            $whereCondition = $this->parseCondition(trim($whereMatches[1]));
            $where = new WhereClause($whereCondition);
            
            if (isset($whereMatches[2])) {
                $limit = (int) $whereMatches[2];
            }
        } elseif (preg_match('/LIMIT\s+(\d+)$/i', $remaining, $limitMatches)) {
            $limit = (int) $limitMatches[1];
        }

        return new SelectQuery($columns, $from, $where, $limit);
    }

    /**
     * Parse column list
     *
     * @param string $columnsString Column specification (e.g., "*" or "col1, col2")
     * @return array Array of column names
     */
    private function parseColumns(string $columnsString): array
    {
        if ($columnsString === '*') {
            return ['*'];
        }

        return array_map('trim', explode(',', $columnsString));
    }

    /**
     * Parse a condition string into a Condition AST node
     *
     * @param string $conditionString The condition string
     * @return Condition The parsed condition
     * @throws QueryException
     */
    private function parseCondition(string $conditionString): Condition
    {
        // Handle logical operators (AND, OR) with proper precedence
        $conditionString = trim($conditionString);
        
        // Check for OR first (lower precedence)
        if (preg_match('/^(.+?)\s+OR\s+(.+)$/i', $conditionString, $matches)) {
            $leftCondition = $this->parseCondition(trim($matches[1]));
            $rightCondition = $this->parseCondition(trim($matches[2]));
            
            return new Condition(
                logicalOperator: 'OR',
                leftCondition: $leftCondition,
                rightCondition: $rightCondition
            );
        }
        
        // Check for AND
        if (preg_match('/^(.+?)\s+AND\s+(.+)$/i', $conditionString, $matches)) {
            $leftCondition = $this->parseCondition(trim($matches[1]));
            $rightCondition = $this->parseCondition(trim($matches[2]));
            
            return new Condition(
                logicalOperator: 'AND',
                leftCondition: $leftCondition,
                rightCondition: $rightCondition
            );
        }

        // Parse simple condition
        return $this->parseSimpleCondition($conditionString);
    }

    /**
     * Parse a simple condition (column operator value)
     *
     * @param string $conditionString The simple condition string
     * @return Condition The parsed condition
     * @throws QueryException
     */
    private function parseSimpleCondition(string $conditionString): Condition
    {
        // Supported operators
        $operators = ['>=', '<=', '!=', '=', '<', '>'];
        
        foreach ($operators as $operator) {
            if (str_contains($conditionString, $operator)) {
                $parts = explode($operator, $conditionString, 2);
                if (count($parts) === 2) {
                    $column = trim($parts[0]);
                    $value = trim($parts[1]);
                    
                    // Parse value (remove quotes if string)
                    $parsedValue = $this->parseValue($value);
                    
                    return new Condition(
                        column: $column,
                        operator: $operator,
                        value: $parsedValue
                    );
                }
            }
        }
        
        throw new QueryException("Invalid condition format: " . $conditionString);
    }

    /**
     * Parse a value from string representation
     *
     * @param string $value The value string
     * @return mixed The parsed value
     */
    private function parseValue(string $value): mixed
    {
        // Remove quotes for strings
        if (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'")) {
            return substr($value, 1, -1);
        }
        
        // Parse numbers
        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }
        
        // Return as string
        return $value;
    }
}