<?php

namespace Dipesh79\LaravelSchemaDocs\Exceptions;

use Exception;
use Throwable;

class SchemaFileNotFoundException extends Exception
{
    public function __construct(string $message = "Laravel Schema Docs yaml file not found. Use 'artisan laravelschemadocs:generate' command to generate it.", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
