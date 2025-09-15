<?php

require_once __DIR__ . '/../vendor/autoload.php';

use QueryLang\Query;
use QueryLang\QueryException;

// Sample data - products
$products = [
    ['id' => 1, 'name' => 'Laptop', 'price' => 999.99, 'category' => 'Electronics', 'stock' => 15],
    ['id' => 2, 'name' => 'Smartphone', 'price' => 699.99, 'category' => 'Electronics', 'stock' => 25],
    ['id' => 3, 'name' => 'Book', 'price' => 19.99, 'category' => 'Books', 'stock' => 100],
    ['id' => 4, 'name' => 'Headphones', 'price' => 149.99, 'category' => 'Electronics', 'stock' => 8],
    ['id' => 5, 'name' => 'Notebook', 'price' => 5.99, 'category' => 'Stationery', 'stock' => 50],
    ['id' => 6, 'name' => 'Tablet', 'price' => 499.99, 'category' => 'Electronics', 'stock' => 12],
];

// Sample data - orders
$orders = [
    ['id' => 1, 'customer' => 'John Doe', 'amount' => 999.99, 'status' => 'completed'],
    ['id' => 2, 'customer' => 'Jane Smith', 'amount' => 149.99, 'status' => 'pending'],
    ['id' => 3, 'customer' => 'Bob Johnson', 'amount' => 699.99, 'status' => 'completed'],
    ['id' => 4, 'customer' => 'Alice Brown', 'amount' => 19.99, 'status' => 'cancelled'],
    ['id' => 5, 'customer' => 'Charlie Wilson', 'amount' => 499.99, 'status' => 'pending'],
];

echo "=== Advanced Usage Examples ===\n\n";

// 1. Multiple data sources
echo "1. Electronics products:\n";
$query = "SELECT name, price FROM \$products WHERE category = 'Electronics'";
$result = Query::run($query, compact('products'));
foreach ($result as $product) {
    echo "  - {$product['name']}: \${$product['price']}\n";
}
echo "\n";

// 2. Price range filtering
echo "2. Products between \$100 and \$500:\n";
$query = "SELECT * FROM \$products WHERE price >= 100 AND price <= 500";
$result = Query::run($query, compact('products'));
foreach ($result as $product) {
    echo "  - {$product['name']}: \${$product['price']}\n";
}
echo "\n";

// 3. Low stock alert
echo "3. Products with low stock (less than 20):\n";
$query = "SELECT name, stock FROM \$products WHERE stock < 20";
$result = Query::run($query, compact('products'));
foreach ($result as $product) {
    echo "  - {$product['name']}: {$product['stock']} units left\n";
}
echo "\n";

// 4. Order status filtering
echo "4. Completed orders:\n";
$query = "SELECT customer, amount FROM \$orders WHERE status = 'completed'";
$result = Query::run($query, compact('orders'));
foreach ($result as $order) {
    echo "  - {$order['customer']}: \${$order['amount']}\n";
}
echo "\n";

// 5. High-value orders
echo "5. Orders over \$500:\n";
$query = "SELECT * FROM \$orders WHERE amount > 500";
$result = Query::run($query, compact('orders'));
foreach ($result as $order) {
    echo "  - {$order['customer']}: \${$order['amount']} ({$order['status']})\n";
}
echo "\n";

// 6. Complex OR condition
echo "6. Electronics OR high-value items:\n";
$query = "SELECT name, price, category FROM \$products WHERE category = 'Electronics' OR price > 200";
$result = Query::run($query, compact('products'));
foreach ($result as $product) {
    echo "  - {$product['name']}: \${$product['price']} ({$product['category']})\n";
}
echo "\n";

// 7. Multiple conditions with different operators
echo "7. Products with good stock AND reasonable price:\n";
$query = "SELECT name, price, stock FROM \$products WHERE stock >= 10 AND price <= 100";
$result = Query::run($query, compact('products'));
foreach ($result as $product) {
    echo "  - {$product['name']}: \${$product['price']} (Stock: {$product['stock']})\n";
}
echo "\n";

// 8. Error handling example
echo "8. Error handling example:\n";
try {
    $query = "SELECT * FROM \$nonexistent";
    $result = Query::run($query, compact('products'));
} catch (QueryException $e) {
    echo "  Error caught: " . $e->getMessage() . "\n";
}
echo "\n";

// 9. Empty result handling
echo "9. Non-existent category:\n";
$query = "SELECT * FROM \$products WHERE category = 'NonExistent'";
$result = Query::run($query, compact('products'));
echo "  Found " . count($result) . " products\n";
echo "\n";

// 10. Performance test with larger dataset
echo "10. Performance test with larger dataset:\n";
$largeDataset = [];
for ($i = 1; $i <= 1000; $i++) {
    $largeDataset[] = [
        'id' => $i,
        'value' => rand(1, 100),
        'category' => ['A', 'B', 'C'][rand(0, 2)],
        'active' => rand(0, 1) === 1
    ];
}

$start = microtime(true);
$query = "SELECT * FROM \$largeDataset WHERE value > 50 AND active = 1 LIMIT 10";
$result = Query::run($query, ['largeDataset' => $largeDataset]);
$end = microtime(true);

echo "  Query executed in " . round(($end - $start) * 1000, 2) . "ms\n";
echo "  Found " . count($result) . " results\n";
echo "\n";