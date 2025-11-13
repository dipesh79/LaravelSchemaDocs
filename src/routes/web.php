<?php

use Dipesh79\LaravelSchemaDocs\Http\Controllers\LaravelSchemaDocsController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('laravel-schema-docs.middleware'))->group(function () {
    Route::controller(LaravelSchemaDocsController::class)->group(function () {
        Route::get('/laravel-schema-docs', 'index')->name('laravelschemadocs.index');
        Route::get('/laravel-schema-docs/table/{name}', 'show')->name('laravelschemadocs.show');
        Route::get('/laravel-schema-docs/erd', 'erd')->name('laravelschemadocs.download');
    });
});
