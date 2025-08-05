<?php

namespace DutchCodingCompany\LaravelJsonSchema\Commands;

use DutchCodingCompany\LaravelJsonSchema\JsonSchemaRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;

class Optimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json-schema:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Caches available json schemas.';

    /**
     * Execute the console command.
     */
    public function handle(FilesystemFactory $filesystem, JsonSchemaRepository $repository): int
    {
        $this->call('json-schema:optimize-clear');

        $filesystem->disk('local')->put(
            $repository->fullCachePath(),
            '<?php return '.var_export($repository->schemaFiles(true), true).';'.PHP_EOL,
        );

        $this->outputComponents()->info('Json schemas cached successfully!');

        return static::SUCCESS;
    }
}
