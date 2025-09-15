<?php

declare(strict_types=1);

namespace QueryLang\Tests;

use PHPUnit\Framework\TestCase;
use QueryLang\Parser;
use QueryLang\Compiler;

class CompilerTest extends TestCase
{
    private Parser $parser;
    private Compiler $compiler;

    protected function setUp(): void
    {
        $this->parser = new Parser();
        $this->compiler = new Compiler();
    }

    public function testCompileBasicSelect(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users");
        $compiled = $this->compiler->compile($ast);
        
        $this->assertEquals('SELECT', $compiled['type']);
        $this->assertEquals('$users', $compiled['from']);
        $this->assertEquals(['*'], $compiled['columns']);
        $this->assertArrayNotHasKey('where', $compiled);
        $this->assertArrayNotHasKey('limit', $compiled);
    }

    public function testCompileSelectWithColumns(): void
    {
        $ast = $this->parser->parse("SELECT name, age FROM \$users");
        $compiled = $this->compiler->compile($ast);
        
        $this->assertEquals(['name', 'age'], $compiled['columns']);
    }

    public function testCompileSelectWithWhere(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE age > 18");
        $compiled = $this->compiler->compile($ast);
        
        $this->assertArrayHasKey('where', $compiled);
        $where = $compiled['where'];
        $this->assertEquals('simple', $where['type']);
        $this->assertEquals('age', $where['column']);
        $this->assertEquals('>', $where['operator']);
        $this->assertEquals(18, $where['value']);
    }

    public function testCompileSelectWithLimit(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users LIMIT 10");
        $compiled = $this->compiler->compile($ast);
        
        $this->assertEquals(10, $compiled['limit']);
    }

    public function testCompileAndCondition(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE age > 18 AND age < 30");
        $compiled = $this->compiler->compile($ast);
        
        $where = $compiled['where'];
        $this->assertEquals('compound', $where['type']);
        $this->assertEquals('AND', $where['logical_operator']);
        
        $left = $where['left'];
        $this->assertEquals('simple', $left['type']);
        $this->assertEquals('age', $left['column']);
        $this->assertEquals('>', $left['operator']);
        $this->assertEquals(18, $left['value']);
        
        $right = $where['right'];
        $this->assertEquals('simple', $right['type']);
        $this->assertEquals('age', $right['column']);
        $this->assertEquals('<', $right['operator']);
        $this->assertEquals(30, $right['value']);
    }

    public function testCompileOrCondition(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE age < 18 OR age > 65");
        $compiled = $this->compiler->compile($ast);
        
        $where = $compiled['where'];
        $this->assertEquals('compound', $where['type']);
        $this->assertEquals('OR', $where['logical_operator']);
    }

    public function testCompileComplexCondition(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE age > 18 AND age < 30 OR name = 'admin'");
        $compiled = $this->compiler->compile($ast);
        
        $where = $compiled['where'];
        $this->assertEquals('compound', $where['type']);
        $this->assertEquals('OR', $where['logical_operator']);
        
        $left = $where['left'];
        $this->assertEquals('compound', $left['type']);
        $this->assertEquals('AND', $left['logical_operator']);
    }

    public function testCompileStringValues(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE name = 'Alice'");
        $compiled = $this->compiler->compile($ast);
        
        $where = $compiled['where'];
        $this->assertEquals('Alice', $where['value']);
    }

    public function testCompileNumericValues(): void
    {
        $ast = $this->parser->parse("SELECT * FROM \$users WHERE score = 85.5");
        $compiled = $this->compiler->compile($ast);
        
        $where = $compiled['where'];
        $this->assertEquals(85.5, $where['value']);
    }
}