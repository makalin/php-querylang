<?php

/**
 * Example runner script for php-querylang
 * 
 * This script demonstrates various features of the QueryLang library
 * by running all example files.
 */

echo "php-querylang Examples\n";
echo "=====================\n\n";

// Check if autoloader exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "Error: Composer autoloader not found.\n";
    echo "Please run 'composer install' first.\n";
    exit(1);
}

// Include autoloader
require_once __DIR__ . '/vendor/autoload.php';

// List of example files to run
$examples = [
    'basic_usage.php' => 'Basic Usage Examples',
    'advanced_usage.php' => 'Advanced Usage Examples',
    'error_handling.php' => 'Error Handling Examples',
];

// Run each example
foreach ($examples as $file => $title) {
    $filePath = __DIR__ . '/examples/' . $file;
    
    if (!file_exists($filePath)) {
        echo "Warning: Example file '{$file}' not found.\n";
        continue;
    }
    
    echo "Running: {$title}\n";
    echo str_repeat('-', strlen($title)) . "\n";
    
    // Capture output
    ob_start();
    include $filePath;
    $output = ob_get_clean();
    
    echo $output;
    echo "\n" . str_repeat('=', 50) . "\n\n";
}

echo "All examples completed!\n";
echo "\nTo run individual examples:\n";
echo "  php examples/basic_usage.php\n";
echo "  php examples/advanced_usage.php\n";
echo "  php examples/error_handling.php\n";
echo "\nTo run tests:\n";
echo "  composer test\n";
echo "\nTo run tests with coverage:\n";
echo "  composer test-coverage\n";