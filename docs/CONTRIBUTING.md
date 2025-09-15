# Contributing Guide

Thank you for your interest in contributing to php-querylang! This document provides guidelines for contributing to the project.

## Getting Started

### Development Environment Setup

1. Fork the repository on GitHub
2. Clone your fork locally:
```bash
git clone https://github.com/YOUR_USERNAME/php-querylang.git
cd php-querylang
```

3. Install dependencies:
```bash
composer install
```

4. Run tests to ensure everything works:
```bash
composer test
```

## Development Workflow

### Branching Strategy

- Create a feature branch from `main`:
```bash
git checkout -b feature/your-feature-name
```

- Use descriptive branch names:
  - `feature/add-order-by-support`
  - `bugfix/fix-parser-edge-case`
  - `docs/update-api-documentation`

### Code Style

- Follow PSR-12 coding standards
- Use PHP 8.0+ features where appropriate
- Add type hints for all parameters and return values
- Use `declare(strict_types=1);` at the top of all PHP files

### Testing

- Write tests for all new functionality
- Ensure all existing tests pass
- Aim for high test coverage
- Test edge cases and error conditions

### Documentation

- Update API documentation for new features
- Add examples for new functionality
- Update README if needed
- Add inline comments for complex logic

## Areas for Contribution

### Planned Features

- [ ] `ORDER BY` clause support
- [ ] `JOIN` operations for nested arrays
- [ ] `INSERT` / `UPDATE` / `DELETE` operations
- [ ] Aggregate functions (`COUNT`, `SUM`, `AVG`, etc.)
- [ ] `GROUP BY` clause
- [ ] `HAVING` clause
- [ ] Subqueries
- [ ] Performance optimizations

### Bug Fixes

- Parser edge cases
- Error handling improvements
- Performance issues
- Memory usage optimizations

### Documentation

- API documentation improvements
- More examples
- Tutorial guides
- Performance benchmarks

## Submitting Changes

### Pull Request Process

1. Ensure your code follows the coding standards
2. Write comprehensive tests
3. Update documentation as needed
4. Commit your changes with clear messages:
```bash
git commit -m "Add ORDER BY clause support

- Implement ORDER BY parsing in Parser class
- Add ORDER BY compilation in Compiler class
- Add ORDER BY execution in Executor class
- Add comprehensive tests for ORDER BY functionality"
```

5. Push to your fork:
```bash
git push origin feature/your-feature-name
```

6. Create a Pull Request on GitHub

### Pull Request Guidelines

- Provide a clear description of changes
- Reference any related issues
- Include test results
- Ensure CI passes
- Request review from maintainers

### Commit Message Format

Use clear, descriptive commit messages:

```
type(scope): brief description

Detailed explanation of changes, if needed.

Fixes #issue-number
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `test`: Test additions/changes
- `refactor`: Code refactoring
- `perf`: Performance improvements

## Code Review Process

### Review Criteria

- Code quality and style
- Test coverage
- Documentation completeness
- Performance implications
- Backward compatibility

### Review Process

1. Automated tests must pass
2. Manual review by maintainers
3. Address feedback promptly
4. Make necessary changes
5. Re-request review when ready

## Testing Guidelines

### Writing Tests

- Use PHPUnit for all tests
- Follow the existing test structure
- Test both success and failure cases
- Include edge cases
- Use descriptive test method names

### Test Structure

```php
<?php

declare(strict_types=1);

namespace QueryLang\Tests;

use PHPUnit\Framework\TestCase;
use QueryLang\Query;

class NewFeatureTest extends TestCase
{
    public function testBasicFunctionality(): void
    {
        // Arrange
        $data = [...];
        $query = "...";
        
        // Act
        $result = Query::run($query, ['data' => $data]);
        
        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals('expected', $result[0]['field']);
    }
    
    public function testEdgeCase(): void
    {
        // Test edge cases
    }
    
    public function testErrorHandling(): void
    {
        // Test error conditions
    }
}
```

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
./vendor/bin/phpunit tests/NewFeatureTest.php

# Run with coverage
composer test-coverage
```

## Performance Considerations

### Benchmarking

- Test with large datasets (1000+ records)
- Measure memory usage
- Compare performance with alternatives
- Document performance characteristics

### Optimization Guidelines

- Avoid unnecessary array copies
- Use efficient algorithms
- Consider memory usage patterns
- Profile critical paths

## Release Process

### Versioning

- Follow Semantic Versioning (SemVer)
- Major: Breaking changes
- Minor: New features (backward compatible)
- Patch: Bug fixes (backward compatible)

### Release Checklist

- [ ] All tests pass
- [ ] Documentation updated
- [ ] Examples updated
- [ ] CHANGELOG.md updated
- [ ] Version bumped in composer.json
- [ ] Tagged release on GitHub

## Community Guidelines

### Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Help others learn and grow
- Follow the golden rule

### Getting Help

- Check existing issues and discussions
- Ask questions in GitHub Discussions
- Join community conversations
- Help others with their questions

## License

By contributing to php-querylang, you agree that your contributions will be licensed under the MIT License.

## Recognition

Contributors will be recognized in:
- README.md contributors section
- Release notes
- Project documentation

Thank you for contributing to php-querylang! 🚀