<?php

namespace DutchCodingCompany\LaravelJsonSchema\Commands;

use DutchCodingCompany\LaravelJsonSchema\JsonSchemaRepository;
use Illuminate\Console\Command;

class OptimizeClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json-schema:optimize-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear json schemas cache.';

    /**
     * Execute the console command.
     */
    public function handle(JsonSchemaRepository $repository): int
    {
        if (file_exists($path = $repository->fullCachePath())) {
            unlink($path);
        }

        $this->outputComponents()->info('Json schemas cache cleared successfully!');

        return static::SUCCESS;
    }
}
