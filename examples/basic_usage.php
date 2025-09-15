<?php

require_once __DIR__ . '/../vendor/autoload.php';

use QueryLang\Query;

// Sample data
$users = [
    ['id' => 1, 'name' => 'Alice', 'age' => 17, 'city' => 'New York'],
    ['id' => 2, 'name' => 'Bob', 'age' => 22, 'city' => 'London'],
    ['id' => 3, 'name' => 'Carol', 'age' => 30, 'city' => 'Paris'],
    ['id' => 4, 'name' => 'David', 'age' => 25, 'city' => 'Tokyo'],
    ['id' => 5, 'name' => 'Eve', 'age' => 19, 'city' => 'Berlin'],
];

echo "=== Basic Usage Examples ===\n\n";

// 1. Select all users
echo "1. Select all users:\n";
$query = "SELECT * FROM \$users";
$result = Query::run($query, compact('users'));
foreach ($result as $user) {
    echo "  - {$user['name']} ({$user['age']} years old, {$user['city']})\n";
}
echo "\n";

// 2. Select specific columns
echo "2. Select only name and age:\n";
$query = "SELECT name, age FROM \$users";
$result = Query::run($query, compact('users'));
foreach ($result as $user) {
    echo "  - {$user['name']}: {$user['age']} years old\n";
}
echo "\n";

// 3. Filter by age
echo "3. Users over 20 years old:\n";
$query = "SELECT * FROM \$users WHERE age > 20";
$result = Query::run($query, compact('users'));
foreach ($result as $user) {
    echo "  - {$user['name']} ({$user['age']} years old)\n";
}
echo "\n";

// 4. Filter by string value
echo "4. Users from London:\n";
$query = "SELECT * FROM \$users WHERE city = 'London'";
$result = Query::run($query, compact('users'));
foreach ($result as $user) {
    echo "  - {$user['name']} from {$user['city']}\n";
}
echo "\n";

// 5. Complex condition with AND
echo "5. Users between 20 and 30 years old:\n";
$query = "SELECT * FROM \$users WHERE age >= 20 AND age <= 30";
$result = Query::run($query, compact('users'));
foreach ($result as $user) {
    echo "  - {$user['name']} ({$user['age']} years old)\n";
}
echo "\n";

// 6. Complex condition with OR
echo "6. Users under 20 OR over 25:\n";
$query = "SELECT * FROM \$users WHERE age < 20 OR age > 25";
$result = Query::run($query, compact('users'));
foreach ($result as $user) {
    echo "  - {$user['name']} ({$user['age']} years old)\n";
}
echo "\n";

// 7. Limit results
echo "7. First 3 users:\n";
$query = "SELECT * FROM \$users LIMIT 3";
$result = Query::run($query, compact('users'));
foreach ($result as $user) {
    echo "  - {$user['name']}\n";
}
echo "\n";

// 8. Combined WHERE and LIMIT
echo "8. First 2 users over 20:\n";
$query = "SELECT * FROM \$users WHERE age > 20 LIMIT 2";
$result = Query::run($query, compact('users'));
foreach ($result as $user) {
    echo "  - {$user['name']} ({$user['age']} years old)\n";
}
echo "\n";