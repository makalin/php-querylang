<?php

declare(strict_types=1);

namespace QueryLang;

/**
 * Main Query class for executing SQL-like queries on PHP arrays
 */
class Query
{
    private Parser $parser;
    private Compiler $compiler;
    private Executor $executor;

    public function __construct()
    {
        $this->parser = new Parser();
        $this->compiler = new Compiler();
        $this->executor = new Executor();
    }

    /**
     * Execute a SQL-like query on PHP arrays
     *
     * @param string $query The SQL-like query string
     * @param array $variables Array of variables available in the query context
     * @return array The query result
     * @throws QueryException
     */
    public function execute(string $query, array $variables = []): array
    {
        try {
            // Parse the query
            $ast = $this->parser->parse($query);
            
            // Compile to PHP operations
            $compiled = $this->compiler->compile($ast);
            
            // Execute against variables
            return $this->executor->execute($compiled, $variables);
        } catch (\Exception $e) {
            throw new QueryException("Query execution failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Static method for quick query execution
     *
     * @param string $query The SQL-like query string
     * @param array $variables Array of variables available in the query context
     * @return array The query result
     * @throws QueryException
     */
    public static function run(string $query, array $variables = []): array
    {
        $queryInstance = new self();
        return $queryInstance->execute($query, $variables);
    }
}