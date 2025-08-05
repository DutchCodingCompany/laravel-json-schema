<?php

namespace DutchCodingCompany\LaravelJsonSchema;

use DutchCodingCompany\LaravelJsonSchema\Commands;
use DutchCodingCompany\LaravelJsonSchema\Contracts\JsonSchemaValidator;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'json-schema');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/json-schema.php', 'json-schema');

        $this->app->singleton(JsonSchemaRepository::class, function ($app) {
            return new JsonSchemaRepository($app->make(FilesystemFactory::class), $app['config']['json-schema'] ?? []);
        });

        $this->app->bind(JsonSchemaValidator::class, JsonSchemaRepository::class);
    }

    public function bootForConsole(): void
    {
        // Register commands
        $this->commands([
            Commands\Optimize::class,
            Commands\OptimizeClear::class,
        ]);

        if (config('json-schema.auto-cache')) {
            // When enabled, auto-cache json schema paths
            $this->optimizes(
                optimize: 'json-schema:optimize',
                clear: 'json-schema:optimize-clear',
                key: 'json-schema',
            );
        }

        $this->publishes([
            __DIR__.'/../config/json-schema.php' => config_path('json-schema.php'),
        ], 'json-schema.config');

        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path('vendor/json-schema'),
        ], 'json-schema.translations');
    }
}
