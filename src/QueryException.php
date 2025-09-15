<?php

declare(strict_types=1);

namespace QueryLang;

/**
 * Exception thrown when query execution fails
 */
class QueryException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}