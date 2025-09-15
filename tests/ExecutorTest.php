<?php

declare(strict_types=1);

namespace QueryLang\Tests;

use PHPUnit\Framework\TestCase;
use QueryLang\Executor;
use QueryLang\QueryException;

class ExecutorTest extends TestCase
{
    private Executor $executor;
    private array $users;

    protected function setUp(): void
    {
        $this->executor = new Executor();
        $this->users = [
            ['id' => 1, 'name' => 'Alice', 'age' => 17],
            ['id' => 2, 'name' => 'Bob', 'age' => 22],
            ['id' => 3, 'name' => 'Carol', 'age' => 30],
        ];
    }

    public function testExecuteBasicSelect(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
        ];

        $result = $this->executor->execute($operations, ['users' => $this->users]);
        
        $this->assertCount(3, $result);
        $this->assertEquals($this->users, $result);
    }

    public function testExecuteSelectWithColumns(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['name', 'age'],
        ];

        $result = $this->executor->execute($operations, ['users' => $this->users]);
        
        $this->assertCount(3, $result);
        $this->assertEquals('Alice', $result[0]['name']);
        $this->assertEquals(17, $result[0]['age']);
        $this->assertArrayNotHasKey('id', $result[0]);
    }

    public function testExecuteWithSimpleWhere(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
            'where' => [
                'type' => 'simple',
                'column' => 'age',
                'operator' => '>',
                'value' => 20,
            ],
        ];

        $result = $this->executor->execute($operations, ['users' => $this->users]);
        
        $this->assertCount(2, $result);
        $this->assertEquals('Bob', $result[0]['name']);
        $this->assertEquals('Carol', $result[1]['name']);
    }

    public function testExecuteWithCompoundWhere(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
            'where' => [
                'type' => 'compound',
                'logical_operator' => 'AND',
                'left' => [
                    'type' => 'simple',
                    'column' => 'age',
                    'operator' => '>',
                    'value' => 18,
                ],
                'right' => [
                    'type' => 'simple',
                    'column' => 'age',
                    'operator' => '<',
                    'value' => 30,
                ],
            ],
        ];

        $result = $this->executor->execute($operations, ['users' => $this->users]);
        
        $this->assertCount(1, $result);
        $this->assertEquals('Bob', $result[0]['name']);
    }

    public function testExecuteWithLimit(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
            'limit' => 2,
        ];

        $result = $this->executor->execute($operations, ['users' => $this->users]);
        
        $this->assertCount(2, $result);
    }

    public function testExecuteWithWhereAndLimit(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
            'where' => [
                'type' => 'simple',
                'column' => 'age',
                'operator' => '>',
                'value' => 18,
            ],
            'limit' => 1,
        ];

        $result = $this->executor->execute($operations, ['users' => $this->users]);
        
        $this->assertCount(1, $result);
        $this->assertEquals('Bob', $result[0]['name']);
    }

    public function testExecuteAllOperators(): void
    {
        $operators = [
            '=' => [['id' => 2, 'name' => 'Bob', 'age' => 22]],
            '!=' => [['id' => 1, 'name' => 'Alice', 'age' => 17], ['id' => 3, 'name' => 'Carol', 'age' => 30]],
            '<' => [['id' => 1, 'name' => 'Alice', 'age' => 17]],
            '>' => [['id' => 2, 'name' => 'Bob', 'age' => 22], ['id' => 3, 'name' => 'Carol', 'age' => 30]],
            '<=' => [['id' => 1, 'name' => 'Alice', 'age' => 17], ['id' => 2, 'name' => 'Bob', 'age' => 22]],
            '>=' => [['id' => 2, 'name' => 'Bob', 'age' => 22], ['id' => 3, 'name' => 'Carol', 'age' => 30]],
        ];

        foreach ($operators as $operator => $expected) {
            $operations = [
                'type' => 'SELECT',
                'from' => '$users',
                'columns' => ['*'],
                'where' => [
                    'type' => 'simple',
                    'column' => 'age',
                    'operator' => $operator,
                    'value' => 22,
                ],
            ];

            $result = $this->executor->execute($operations, ['users' => $this->users]);
            $this->assertEquals($expected, $result, "Failed for operator: {$operator}");
        }
    }

    public function testExecuteWithStringValues(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
            'where' => [
                'type' => 'simple',
                'column' => 'name',
                'operator' => '=',
                'value' => 'Alice',
            ],
        ];

        $result = $this->executor->execute($operations, ['users' => $this->users]);
        
        $this->assertCount(1, $result);
        $this->assertEquals('Alice', $result[0]['name']);
    }

    public function testExecuteWithMissingColumn(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
            'where' => [
                'type' => 'simple',
                'column' => 'nonexistent',
                'operator' => '=',
                'value' => 'value',
            ],
        ];

        $result = $this->executor->execute($operations, ['users' => $this->users]);
        
        $this->assertCount(0, $result);
    }

    public function testExecuteWithMissingVariable(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$nonexistent',
            'columns' => ['*'],
        ];

        $this->expectException(QueryException::class);
        $this->executor->execute($operations, []);
    }

    public function testExecuteWithNonArrayVariable(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$string',
            'columns' => ['*'],
        ];

        $this->expectException(QueryException::class);
        $this->executor->execute($operations, ['string' => 'not an array']);
    }

    public function testExecuteWithInvalidOperationType(): void
    {
        $operations = [
            'type' => 'INVALID',
            'from' => '$users',
            'columns' => ['*'],
        ];

        $this->expectException(QueryException::class);
        $this->executor->execute($operations, ['users' => $this->users]);
    }

    public function testExecuteWithInvalidConditionType(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
            'where' => [
                'type' => 'invalid',
            ],
        ];

        $this->expectException(QueryException::class);
        $this->executor->execute($operations, ['users' => $this->users]);
    }

    public function testExecuteWithInvalidLogicalOperator(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
            'where' => [
                'type' => 'compound',
                'logical_operator' => 'INVALID',
                'left' => [
                    'type' => 'simple',
                    'column' => 'age',
                    'operator' => '>',
                    'value' => 18,
                ],
                'right' => [
                    'type' => 'simple',
                    'column' => 'age',
                    'operator' => '<',
                    'value' => 30,
                ],
            ],
        ];

        $this->expectException(QueryException::class);
        $this->executor->execute($operations, ['users' => $this->users]);
    }

    public function testExecuteWithInvalidSimpleOperator(): void
    {
        $operations = [
            'type' => 'SELECT',
            'from' => '$users',
            'columns' => ['*'],
            'where' => [
                'type' => 'simple',
                'column' => 'age',
                'operator' => 'INVALID',
                'value' => 18,
            ],
        ];

        $this->expectException(QueryException::class);
        $this->executor->execute($operations, ['users' => $this->users]);
    }
}