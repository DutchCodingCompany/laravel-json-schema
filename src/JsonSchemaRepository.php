<?php

namespace DutchCodingCompany\LaravelJsonSchema;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use JsonException;
use LogicException;
use Swaggest\JsonSchema\Context;
use Swaggest\JsonSchema\InvalidValue;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\SchemaContract;

class JsonSchemaRepository implements Contracts\JsonSchemaValidator
{
    protected ?Context $schemaContext = null;

    /**
     * @var array<string, string>|null
     */
    protected ?array $files = null;

    /**
     * @var array<string, \Swaggest\JsonSchema\SchemaContract>
     */
    protected array $schemaCache = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        protected FilesystemFactory $filesystem,
        protected readonly array $config,
    ) {
        //
    }

    public function setContext(Context | null $context = null): static
    {
        $this->schemaContext = $context;

        return $this;
    }

    public function getContext(): ?Context
    {
        return $this->schemaContext;
    }

    public function getConfig(string $key): mixed
    {
        return data_get($this->config, $key);
    }

    protected function disk(): Filesystem
    {
        return $this->filesystem->disk(
            $this->getConfig('disk')
        );
    }

    final public function fullCachePath(): string
    {
        return App::bootstrapPath('cache/json_schemas.php');
    }

    /**
     * @return array<string, string>
     */
    protected function findSchemaFiles(): array
    {
        if ($this->filesystem->disk('local')->exists($path = $this->fullCachePath())) {
            return include $path;
        }

        // Grab files
        $files = $this->disk()->files(
            $directory = $this->getConfig('directory'),
            (bool) $this->getConfig('file-search.recursive')
        );

        // Grab filter pattern
        $filter = $this->getConfig('file-search.filter');
        $schemaName = $this->getConfig('file-search.schema-name');

        // Remove non matching files
        return collect($files)->filter(function ($file) use ($filter) {
            if ($filter === false || is_null($filter)) {
                return false;
            }

            return preg_match($filter, $file) != false;
        })->mapWithKeys(function ($file) use ($directory, $filter, $schemaName) {
            $originalPath = $file;

            if (! is_null($directory) && Str::length($directory) > 1) {
                $file = Str::replaceFirst(Str::finish($directory, '/'), '', $file);
            }

            return [preg_replace($filter, $schemaName, $file) => $originalPath];
        })->toArray();
    }

    /**
     * @return array<string, string>
     */
    public function schemaFiles(bool $refresh = false): array
    {
        if ($refresh) {
            $this->files = null;
        }

        return $this->files ??= $this->findSchemaFiles();
    }

    public function hasSchema(string $schema): bool
    {
        return array_key_exists($schema, $this->schemaFiles()) || array_key_exists($schema, $this->schemaCache);
    }

    protected function importSchema(string $schema): SchemaContract
    {
        $file = $this->disk()->get($this->schemaFiles()[$schema]);

        if ($file === null) {
            throw new LogicException('Schema "'.$schema.'" was discovered, but file does not exist or is empty.');
        }

        return Schema::import(json_decode($file, flags: JSON_THROW_ON_ERROR), $this->getContext());
    }

    public function getSchema(string $schema): ?SchemaContract
    {
        if (! $this->hasSchema($schema)) {
            return null;
        }

        return $this->schemaCache[$schema] ??= $this->importSchema($schema);
    }

    public function validate(string $schemaName, string $data): Contracts\JsonSchemaValidationResult
    {
        $schema = $this->getSchema($schemaName);

        if ($schema === null) {
            return new ValidationResult(false, $schemaName, data: $data, exception: new LogicException('Schema "'.$schemaName.'" does not exist.'));
        }

        $result = false;
        $exception = null;
        try {
            $schema->in(json_decode($data, flags: JSON_THROW_ON_ERROR));

            $result = true;
        } catch (InvalidValue | JsonException $e) {
            $exception = $e;
        }

        return new ValidationResult($result, $schemaName, $schema, $data, $exception);
    }
}
