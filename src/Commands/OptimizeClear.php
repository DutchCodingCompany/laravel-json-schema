<?php

namespace DutchCodingCompany\LaravelJsonSchema\Commands;

use DutchCodingCompany\LaravelJsonSchema\JsonSchemaRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

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
    public function handle(FilesystemFactory $filesystem, JsonSchemaRepository $repository): int
    {
        $filesystem->disk('local')->delete($repository->fullCachePath());

        $this->outputComponents()->info('Json schemas cache cleared successfully!');

        return static::SUCCESS;
    }
}
