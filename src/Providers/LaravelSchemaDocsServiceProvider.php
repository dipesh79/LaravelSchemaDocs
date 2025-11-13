<?php

namespace Dipesh79\LaravelSchemaDocs\Providers;

use Dipesh79\LaravelSchemaDocs\Commands\GenerateDocsCommand;
use Illuminate\Support\ServiceProvider;

class LaravelSchemaDocsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/laravel-schema-docs.php' => config_path('laravel-schema-docs.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laravelschemadocs');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateDocsCommand::class,
            ]);
        }
    }
}
