# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release with basic SELECT query support
- WHERE clause with comparison operators (=, !=, <, >, <=, >=)
- AND/OR logical operators for compound conditions
- LIMIT clause for result limiting
- Column selection (SELECT * or specific columns)
- String and numeric value support
- Comprehensive error handling with QueryException
- Full test suite with PHPUnit
- Examples and documentation

### Features
- SQL-like syntax compilation to native PHP array operations
- Zero dependencies (except PHP 8.0+)
- Lightweight and fast execution
- Extensible parser and compiler architecture
- Support for complex nested conditions with parentheses

## [1.0.0] - 2025-01-XX

### Added
- Initial release
- Core Query class with static `run()` method
- Parser for SQL-like query strings
- Compiler for AST to PHP operations conversion
- Executor for running compiled operations
- AST (Abstract Syntax Tree) node classes
- Comprehensive test coverage
- Examples and documentation
- Composer package configuration
- MIT License

### Supported Syntax
- `SELECT * FROM $array`
- `SELECT column1, column2 FROM $array`
- `WHERE` conditions with all comparison operators
- `AND` and `OR` logical operators
- `LIMIT n` for result limiting
- String values with single or double quotes
- Numeric values (integers and floats)

### Performance
- Optimized for small to medium datasets (< 10,000 records)
- Memory efficient array operations
- Fast parsing and compilation
- No external dependencies

### Documentation
- Complete API documentation
- Installation guide
- Contributing guidelines
- Usage examples
- Error handling guide