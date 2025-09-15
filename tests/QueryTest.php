<?php

declare(strict_types=1);

namespace QueryLang\Tests;

use PHPUnit\Framework\TestCase;
use QueryLang\Query;
use QueryLang\QueryException;

class QueryTest extends TestCase
{
    private array $users;

    protected function setUp(): void
    {
        $this->users = [
            ['id' => 1, 'name' => 'Alice', 'age' => 17, 'city' => 'New York'],
            ['id' => 2, 'name' => 'Bob', 'age' => 22, 'city' => 'London'],
            ['id' => 3, 'name' => 'Carol', 'age' => 30, 'city' => 'Paris'],
            ['id' => 4, 'name' => 'David', 'age' => 25, 'city' => 'Tokyo'],
            ['id' => 5, 'name' => 'Eve', 'age' => 19, 'city' => 'Berlin'],
        ];
    }

    public function testBasicSelectAll(): void
    {
        $query = "SELECT * FROM \$users";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(5, $result);
        $this->assertEquals($this->users, $result);
    }

    public function testSelectSpecificColumns(): void
    {
        $query = "SELECT name, age FROM \$users";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(5, $result);
        $this->assertEquals('Alice', $result[0]['name']);
        $this->assertEquals(17, $result[0]['age']);
        $this->assertArrayNotHasKey('id', $result[0]);
        $this->assertArrayNotHasKey('city', $result[0]);
    }

    public function testWhereEquals(): void
    {
        $query = "SELECT * FROM \$users WHERE age = 22";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(1, $result);
        $this->assertEquals('Bob', $result[0]['name']);
    }

    public function testWhereGreaterThan(): void
    {
        $query = "SELECT * FROM \$users WHERE age > 20";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(3, $result);
        $this->assertEquals('Bob', $result[0]['name']);
        $this->assertEquals('Carol', $result[1]['name']);
        $this->assertEquals('David', $result[2]['name']);
    }

    public function testWhereLessThan(): void
    {
        $query = "SELECT * FROM \$users WHERE age < 20";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(2, $result);
        $this->assertEquals('Alice', $result[0]['name']);
        $this->assertEquals('Eve', $result[1]['name']);
    }

    public function testWhereGreaterThanOrEqual(): void
    {
        $query = "SELECT * FROM \$users WHERE age >= 25";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(2, $result);
        $this->assertEquals('Carol', $result[0]['name']);
        $this->assertEquals('David', $result[1]['name']);
    }

    public function testWhereLessThanOrEqual(): void
    {
        $query = "SELECT * FROM \$users WHERE age <= 20";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(2, $result);
        $this->assertEquals('Alice', $result[0]['name']);
        $this->assertEquals('Eve', $result[1]['name']);
    }

    public function testWhereNotEquals(): void
    {
        $query = "SELECT * FROM \$users WHERE age != 22";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(4, $result);
        $names = array_column($result, 'name');
        $this->assertNotContains('Bob', $names);
    }

    public function testWhereWithStringValue(): void
    {
        $query = "SELECT * FROM \$users WHERE name = 'Alice'";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(1, $result);
        $this->assertEquals('Alice', $result[0]['name']);
    }

    public function testWhereWithDoubleQuotedString(): void
    {
        $query = 'SELECT * FROM $users WHERE city = "London"';
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(1, $result);
        $this->assertEquals('Bob', $result[0]['name']);
    }

    public function testAndCondition(): void
    {
        $query = "SELECT * FROM \$users WHERE age > 20 AND age < 30";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(2, $result);
        $this->assertEquals('Bob', $result[0]['name']);
        $this->assertEquals('David', $result[1]['name']);
    }

    public function testOrCondition(): void
    {
        $query = "SELECT * FROM \$users WHERE age < 20 OR age > 25";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(3, $result);
        $names = array_column($result, 'name');
        $this->assertContains('Alice', $names);
        $this->assertContains('Carol', $names);
        $this->assertContains('David', $names);
    }

    public function testComplexAndOrCondition(): void
    {
        $query = "SELECT * FROM \$users WHERE (age > 20 AND age < 30) OR name = 'Alice'";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(3, $result);
        $names = array_column($result, 'name');
        $this->assertContains('Alice', $names);
        $this->assertContains('Bob', $names);
        $this->assertContains('David', $names);
    }

    public function testLimit(): void
    {
        $query = "SELECT * FROM \$users LIMIT 3";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(3, $result);
    }

    public function testWhereWithLimit(): void
    {
        $query = "SELECT * FROM \$users WHERE age > 20 LIMIT 2";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(2, $result);
    }

    public function testEmptyResult(): void
    {
        $query = "SELECT * FROM \$users WHERE age > 100";
        $result = Query::run($query, ['users' => $this->users]);
        
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testEmptyArray(): void
    {
        $query = "SELECT * FROM \$empty";
        $result = Query::run($query, ['empty' => []]);
        
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testInvalidQueryThrowsException(): void
    {
        $this->expectException(QueryException::class);
        Query::run("INVALID QUERY", ['users' => $this->users]);
    }

    public function testMissingVariableThrowsException(): void
    {
        $this->expectException(QueryException::class);
        Query::run("SELECT * FROM \$nonexistent", []);
    }

    public function testNonArrayVariableThrowsException(): void
    {
        $this->expectException(QueryException::class);
        Query::run("SELECT * FROM \$string", ['string' => 'not an array']);
    }

    public function testEmptyQueryThrowsException(): void
    {
        $this->expectException(QueryException::class);
        Query::run("", ['users' => $this->users]);
    }

    public function testNumericValues(): void
    {
        $data = [
            ['id' => 1, 'score' => 85.5],
            ['id' => 2, 'score' => 92.0],
            ['id' => 3, 'score' => 78.3],
        ];

        $query = "SELECT * FROM \$data WHERE score >= 85";
        $result = Query::run($query, ['data' => $data]);
        
        $this->assertCount(2, $result);
    }
}