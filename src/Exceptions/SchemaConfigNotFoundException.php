<?php

namespace Dipesh79\LaravelSchemaDocs\Exceptions;

use Exception;
use Throwable;

class SchemaConfigNotFoundException extends Exception
{
    public function __construct(string $message = 'Laravel Schema Docs config file not found. Use artisan vendor:publish --provider="Dipesh79\LaravelSchemaDocs\Providers\LaravelSchemaDocsServiceProvider" command to generate it.', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
