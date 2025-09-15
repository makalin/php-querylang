# php-querylang

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D8.0-777BB4?logo=php)](https://www.php.net/)

**SQL-like syntax compiled to native PHP arrays.**  
Write queries in a familiar style (`SELECT * FROM $users WHERE age > 18`) and run them natively against arrays without a database engine.

---

## Features

- ✅ SQL-inspired query syntax  
- ✅ Compile queries directly to PHP array operations  
- ✅ No database required – works with arrays/lists  
- ✅ Lightweight, zero dependencies  
- ✅ Extendable parser & compiler  

---

## Example

```php
use QueryLang\Query;

$users = [
    ['id' => 1, 'name' => 'Alice', 'age' => 17],
    ['id' => 2, 'name' => 'Bob',   'age' => 22],
    ['id' => 3, 'name' => 'Carol', 'age' => 30],
];

$query = "SELECT * FROM \$users WHERE age > 18";

$result = Query::run($query, compact('users'));

print_r($result);
````

**Output:**

```php
[
    ['id' => 2, 'name' => 'Bob',   'age' => 22],
    ['id' => 3, 'name' => 'Carol', 'age' => 30],
]
```

---

## Installation

```bash
composer require makalin/php-querylang
```

---

## Supported Syntax

* `SELECT * FROM $array`
* `WHERE` with `=`, `!=`, `<`, `>`, `<=`, `>=`
* `AND`, `OR` conditions
* `LIMIT n`
* (More operators coming soon)

---

## Roadmap

* [ ] Add `ORDER BY`
* [ ] Add `JOIN` (nested arrays)
* [ ] Add `INSERT` / `UPDATE` support

---

## License

MIT © 2025 [Mehmet T. AKALIN](https://github.com/makalin)
