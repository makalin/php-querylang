<?php

declare(strict_types=1);

namespace QueryLang\AST;

/**
 * Base class for all AST nodes
 */
abstract class Node
{
    /**
     * Get the node type
     */
    abstract public function getType(): string;
}