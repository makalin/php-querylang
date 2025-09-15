<?php

declare(strict_types=1);

namespace QueryLang\Tests;

use PHPUnit\Framework\TestCase;
use QueryLang\Parser;
use QueryLang\QueryException;

class ParserTest extends TestCase
{
    private Parser $parser;

    protected function setUp(): void
    {
        $this->parser = new Parser();
    }

    public function testParseBasicSelect(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users");
        
        $this->assertEquals('SELECT', $ast->getType());
        $this->assertEquals(['*'], $ast->columns);
        $this->assertEquals('$users', $ast->from);
        $this->assertNull($ast->where);
        $this->assertNull($ast->limit);
    }

    public function testParseSelectWithColumns(): void
    {
        $ast = $this->parser->parse("SELECT name, age FROM \$users");
        
        $this->assertEquals(['name', 'age'], $ast->columns);
        $this->assertEquals('$users', $ast->from);
    }

    public function testParseSelectWithWhere(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE age > 18");
        
        $this->assertNotNull($ast->where);
        $this->assertEquals('WHERE', $ast->where->getType());
        $this->assertTrue($ast->where->condition->isSimple());
        $this->assertEquals('age', $ast->where->condition->column);
        $this->assertEquals('>', $ast->where->condition->operator);
        $this->assertEquals(18, $ast->where->condition->value);
    }

    public function testParseSelectWithLimit(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users LIMIT 10");
        
        $this->assertEquals(10, $ast->limit);
    }

    public function testParseSelectWithWhereAndLimit(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE age > 18 LIMIT 5");
        
        $this->assertNotNull($ast->where);
        $this->assertEquals(5, $ast->limit);
    }

    public function testParseStringValues(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE name = 'Alice'");
        
        $condition = $ast->where->condition;
        $this->assertEquals('name', $condition->column);
        $this->assertEquals('=', $condition->operator);
        $this->assertEquals('Alice', $condition->value);
    }

    public function testParseDoubleQuotedStringValues(): void
    {
        $ast = $this->parser->parse('SELECT * FROM $users WHERE city = "London"');
        
        $condition = $ast->where->condition;
        $this->assertEquals('city', $condition->column);
        $this->assertEquals('=', $condition->operator);
        $this->assertEquals('London', $condition->value);
    }

    public function testParseNumericValues(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE score = 85.5");
        
        $condition = $ast->where->condition;
        $this->assertEquals('score', $condition->column);
        $this->assertEquals('=', $condition->operator);
        $this->assertEquals(85.5, $condition->value);
    }

    public function testParseAndCondition(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE age > 18 AND age < 30");
        
        $condition = $ast->where->condition;
        $this->assertTrue($condition->isCompound());
        $this->assertEquals('AND', $condition->logicalOperator);
        $this->assertTrue($condition->leftCondition->isSimple());
        $this->assertTrue($condition->rightCondition->isSimple());
    }

    public function testParseOrCondition(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE age < 18 OR age > 65");
        
        $condition = $ast->where->condition;
        $this->assertTrue($condition->isCompound());
        $this->assertEquals('OR', $condition->logicalOperator);
    }

    public function testParseComplexCondition(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE age > 18 AND age < 30 OR name = 'admin'");
        
        $condition = $ast->where->condition;
        $this->assertTrue($condition->isCompound());
        $this->assertEquals('OR', $condition->logicalOperator);
        
        $leftCondition = $condition->leftCondition;
        $this->assertTrue($leftCondition->isCompound());
        $this->assertEquals('AND', $leftCondition->logicalOperator);
    }

    public function testParseAllOperators(): void
    {
        $operators = ['=', '!=', '<', '>', '<=', '>='];
        
        foreach ($operators as $operator) {
            $query = "SELECT * FROM \$users WHERE age {$operator} 25";
            $ast = $this->parser->parse($query);
            
            $condition = $ast->where->condition;
            $this->assertEquals('age', $condition->column);
            $this->assertEquals($operator, $condition->operator);
            $this->assertEquals(25, $condition->value);
        }
    }

    public function testParseEmptyQueryThrowsException(): void
    {
        $this->expectException(QueryException::class);
        $this->parser->parse("");
    }

    public function testParseInvalidQueryThrowsException(): void
    {
        $this->expectException(QueryException::class);
        $this->parser->parse("INVALID QUERY");
    }

    public function testParseInvalidConditionThrowsException(): void
    {
        $this->expectException(QueryException::class);
        $this->parser->parse("SELECT * FROM \$users WHERE invalid condition");
    }
}