# API Documentation

## Query Class

The main entry point for executing SQL-like queries on PHP arrays.

### Methods

#### `Query::run(string $query, array $variables = []): array`

Static method for quick query execution.

**Parameters:**
- `$query` (string): The SQL-like query string
- `$variables` (array): Array of variables available in the query context

**Returns:**
- `array`: The query result

**Throws:**
- `QueryException`: When query execution fails

**Example:**
```php
use QueryLang\Query;

$users = [
    ['id' => 1, 'name' => 'Alice', 'age' => 17],
    ['id' => 2, 'name' => 'Bob', 'age' => 22],
];

$result = Query::run("SELECT * FROM \$users WHERE age > 18", ['users' => $users]);
```

#### `execute(string $query, array $variables = []): array`

Instance method for query execution (same as static method).

**Parameters:**
- `$query` (string): The SQL-like query string
- `$variables` (array): Array of variables available in the query context

**Returns:**
- `array`: The query result

**Throws:**
- `QueryException`: When query execution fails

**Example:**
```php
use QueryLang\Query;

$query = new Query();
$result = $query->execute("SELECT name FROM \$users WHERE age > 20", ['users' => $users]);
```

## QueryException Class

Exception thrown when query execution fails.

### Constructor

#### `__construct(string $message = "", int $code = 0, ?\Throwable $previous = null)`

**Parameters:**
- `$message` (string): Exception message
- `$code` (int): Exception code
- `$previous` (?\Throwable): Previous exception for chaining

## Supported Query Syntax

### SELECT Statement

```sql
SELECT columns FROM $variable [WHERE condition] [LIMIT number]
```

#### Columns
- `*` - Select all columns
- `column1, column2, ...` - Select specific columns

#### FROM Clause
- Must reference a variable in the context using `$variableName` syntax
- Variable must contain an array

#### WHERE Clause
- Simple conditions: `column operator value`
- Compound conditions: `condition1 AND condition2`, `condition1 OR condition2`
- Supports parentheses for grouping

#### Operators
- `=` - Equal
- `!=` - Not equal
- `<` - Less than
- `>` - Greater than
- `<=` - Less than or equal
- `>=` - Greater than or equal

#### LIMIT Clause
- `LIMIT number` - Limit the number of results

### Value Types

#### Strings
- Single quotes: `'value'`
- Double quotes: `"value"`

#### Numbers
- Integers: `42`
- Floats: `3.14`

#### Variables
- Referenced with `$` prefix: `$variableName`

### Examples

```sql
-- Basic select
SELECT * FROM $users

-- Select specific columns
SELECT name, age FROM $users

-- Simple condition
SELECT * FROM $users WHERE age > 18

-- String condition
SELECT * FROM $users WHERE name = 'Alice'

-- Compound condition
SELECT * FROM $users WHERE age > 18 AND age < 30

-- OR condition
SELECT * FROM $users WHERE age < 18 OR age > 65

-- With limit
SELECT * FROM $users WHERE age > 20 LIMIT 5

-- Complex condition
SELECT * FROM $users WHERE (age > 18 AND age < 30) OR name = 'admin'
```

## Error Handling

All query execution errors throw `QueryException`. Common error scenarios:

- Invalid query syntax
- Missing variables
- Non-array variables
- Empty queries
- Invalid condition formats

**Example:**
```php
try {
    $result = Query::run("SELECT * FROM \$nonexistent", []);
} catch (QueryException $e) {
    echo "Query failed: " . $e->getMessage();
}
```