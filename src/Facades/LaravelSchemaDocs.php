<?php

namespace Dipesh79\LaravelSchemaDocs\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelSchemaDocs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Dipesh79\LaravelSchemaDocs\Services\LaravelSchemaDocs::class;
    }
}
