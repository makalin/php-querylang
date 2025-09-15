# Installation Guide

## Requirements

- PHP 8.0 or higher
- Composer (for dependency management)

## Installation

### Using Composer (Recommended)

1. Install via Composer:
```bash
composer require makalin/php-querylang
```

2. Use in your PHP code:
```php
require_once 'vendor/autoload.php';

use QueryLang\Query;

$result = Query::run("SELECT * FROM \$users WHERE age > 18", ['users' => $users]);
```

### Manual Installation

1. Download or clone the repository:
```bash
git clone https://github.com/makalin/php-querylang.git
cd php-querylang
```

2. Install dependencies:
```bash
composer install
```

3. Include the autoloader in your project:
```php
require_once 'path/to/php-querylang/vendor/autoload.php';
```

## Development Setup

### Prerequisites

- PHP 8.0+
- Composer
- PHPUnit (for running tests)

### Setup Steps

1. Clone the repository:
```bash
git clone https://github.com/makalin/php-querylang.git
cd php-querylang
```

2. Install dependencies:
```bash
composer install
```

3. Run tests:
```bash
composer test
```

4. Run tests with coverage:
```bash
composer test-coverage
```

### Project Structure

```
php-querylang/
├── src/                    # Source code
│   ├── Query.php          # Main Query class
│   ├── QueryException.php # Exception class
│   ├── Parser.php         # SQL parser
│   ├── Compiler.php       # Query compiler
│   ├── Executor.php       # Query executor
│   └── AST/               # Abstract Syntax Tree nodes
├── tests/                  # Test files
├── examples/              # Usage examples
├── docs/                  # Documentation
├── composer.json          # Composer configuration
├── phpunit.xml           # PHPUnit configuration
└── README.md             # Project documentation
```

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage report
composer test-coverage

# Run specific test file
./vendor/bin/phpunit tests/QueryTest.php
```

### Test Coverage

The project includes comprehensive test coverage for:
- Query parsing
- Query compilation
- Query execution
- Error handling
- Edge cases

## Examples

### Basic Usage

```php
<?php
require_once 'vendor/autoload.php';

use QueryLang\Query;

$users = [
    ['id' => 1, 'name' => 'Alice', 'age' => 17],
    ['id' => 2, 'name' => 'Bob', 'age' => 22],
];

$result = Query::run("SELECT * FROM \$users WHERE age > 18", ['users' => $users]);
print_r($result);
```

### Advanced Usage

```php
<?php
require_once 'vendor/autoload.php';

use QueryLang\Query;

$products = [
    ['id' => 1, 'name' => 'Laptop', 'price' => 999.99, 'category' => 'Electronics'],
    ['id' => 2, 'name' => 'Book', 'price' => 19.99, 'category' => 'Books'],
];

// Complex query
$query = "SELECT name, price FROM \$products WHERE category = 'Electronics' AND price > 500";
$result = Query::run($query, ['products' => $products]);
```

## Troubleshooting

### Common Issues

1. **Autoloader not found**
   - Ensure Composer is installed
   - Run `composer install`
   - Check the path to `vendor/autoload.php`

2. **PHP version compatibility**
   - Ensure PHP 8.0 or higher is installed
   - Check with `php --version`

3. **Memory issues with large datasets**
   - Consider using `LIMIT` in queries
   - Process data in chunks if necessary

### Getting Help

- Check the [API Documentation](API.md)
- Review the [examples](examples/)
- Run the test suite to verify installation
- Open an issue on GitHub for bugs or feature requests