<?php

require_once __DIR__ . '/../vendor/autoload.php';

use QueryLang\Query;
use QueryLang\QueryException;

echo "=== Error Handling Examples ===\n\n";

$users = [
    ['id' => 1, 'name' => 'Alice', 'age' => 17],
    ['id' => 2, 'name' => 'Bob', 'age' => 22],
];

// 1. Invalid query syntax
echo "1. Invalid query syntax:\n";
try {
    $query = "INVALID QUERY SYNTAX";
    $result = Query::run($query, compact('users'));
} catch (QueryException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 2. Missing variable
echo "2. Missing variable:\n";
try {
    $query = "SELECT * FROM \$nonexistent";
    $result = Query::run($query, compact('users'));
} catch (QueryException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Non-array variable
echo "3. Non-array variable:\n";
try {
    $query = "SELECT * FROM \$string";
    $result = Query::run($query, ['string' => 'not an array']);
} catch (QueryException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Empty query
echo "4. Empty query:\n";
try {
    $query = "";
    $result = Query::run($query, compact('users'));
} catch (QueryException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Invalid condition format
echo "5. Invalid condition format:\n";
try {
    $query = "SELECT * FROM \$users WHERE invalid condition";
    $result = Query::run($query, compact('users'));
} catch (QueryException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 6. Missing SELECT keyword
echo "6. Missing SELECT keyword:\n";
try {
    $query = "* FROM \$users";
    $result = Query::run($query, compact('users'));
} catch (QueryException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 7. Missing FROM keyword
echo "7. Missing FROM keyword:\n";
try {
    $query = "SELECT * \$users";
    $result = Query::run($query, compact('users'));
} catch (QueryException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 8. Valid query that returns empty result
echo "8. Valid query that returns empty result:\n";
try {
    $query = "SELECT * FROM \$users WHERE age > 100";
    $result = Query::run($query, compact('users'));
    echo "  Success: Found " . count($result) . " results\n";
} catch (QueryException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 9. Query with non-existent column (should not throw error, just return empty)
echo "9. Query with non-existent column:\n";
try {
    $query = "SELECT * FROM \$users WHERE nonexistent_column = 'value'";
    $result = Query::run($query, compact('users'));
    echo "  Success: Found " . count($result) . " results (expected 0)\n";
} catch (QueryException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 10. Demonstrating proper error handling pattern
echo "10. Proper error handling pattern:\n";
function safeQuery(string $query, array $variables): array
{
    try {
        return Query::run($query, $variables);
    } catch (QueryException $e) {
        echo "  Query failed: " . $e->getMessage() . "\n";
        return [];
    }
}

$result = safeQuery("SELECT * FROM \$users WHERE age > 20", compact('users'));
echo "  Safe query returned " . count($result) . " results\n";

$result = safeQuery("INVALID QUERY", compact('users'));
echo "  Invalid query handled gracefully\n";
echo "\n";