<?php

namespace DutchCodingCompany\LaravelJsonSchema;

use DutchCodingCompany\LaravelJsonSchema\Rules\JsonSchemaRule;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Support\Facades\Validator;

class ServiceProvider extends LaravelServiceProvider {

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/json-schema.php', 'json-schema');

        $this->app->singleton(JsonSchemaRepository::class, function($app) {
            return new JsonSchemaRepository($app->make(FilesystemFactory::class), $app['config']['json-schema'] ?? []);
        });
    }

    public function bootForConsole()
    {
        $this->publishes([
            __DIR__.'/../config/json-schema.php' => config_path('json-schema.php'),
        ], 'json-schema.config');
    }
}