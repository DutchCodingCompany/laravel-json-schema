<?php

namespace DutchCodingCompany\LaravelJsonSchema\Commands;

use DutchCodingCompany\LaravelJsonSchema\JsonSchemaRepository;
use Illuminate\Console\Command;

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
    public function handle(JsonSchemaRepository $repository): int
    {
        $this->call('json-schema:optimize-clear');

        file_put_contents(
            $repository->fullCachePath(),
            '<?php return '.var_export($repository->schemaFiles(true), true).';'.PHP_EOL,
        );

        $this->outputComponents()->info('Json schemas cached successfully!');

        return static::SUCCESS;
    }
}
