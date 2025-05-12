<?php

namespace DutchCodingCompany\LaravelJsonSchema;

use Illuminate\Support\Str;
use Swaggest\JsonSchema\SchemaContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use JsonException;
use Swaggest\JsonSchema\Context;
use Swaggest\JsonSchema\InvalidValue;
use Swaggest\JsonSchema\Schema;

class JsonSchemaRepository implements Contracts\JsonSchemaValidator
{
    private FilesystemFactory $filesystem;
    private array $config;
    private ?Context $schemaContext = null;

    protected ?array $files = null;

    public function __construct(FilesystemFactory $filesystem, array $config)
    {
        $this->filesystem = $filesystem;
        $this->config = $config;
    }

    public function setContext(Context | null $context = null): self
    {
        $this->schemaContext = $context;

        return $this;
    }

    public function getContext(): ?Context
    {
        return $this->schemaContext;
    }

    public function getConfig(string $key)
    {
        return data_get($this->config, $key);
    }

    protected function disk(): Filesystem
    {
        return $this->filesystem->disk(
            $this->getConfig('disk')
        );
    }

    protected function findSchemaFiles(): array
    {
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
        })->mapWithKeys(function($file) use ($directory, $filter, $schemaName) {
            $originalPath = $file;

            if (! is_null($directory) && Str::length($directory) > 1) {
                $file = Str::replaceFirst(Str::finish($directory, '/'), '', $file);
            }

            return [preg_replace($filter, $schemaName, $file) => $originalPath];
        })->toArray();
    }

    public function schemaFiles(bool $refresh = false): array
    {
        if ($refresh) {
            $this->files = null;
        }

        return $this->files ??= $this->findSchemaFiles();
    }

    public function hasSchema(string $schema): bool
    {
        return array_key_exists($schema, $this->schemaFiles());
    }

    protected function findSchema(string $schema): SchemaContract
    {
        $file = $this->disk()->get($this->schemaFiles()[$schema]);

        return Schema::import(json_decode($file, false, 512, JSON_THROW_ON_ERROR), $this->getContext());
    }

    public function getSchema(string $schema): ?SchemaContract
    {
        if (! $this->hasSchema($schema)) {
            return null;
        }

        return $this->schemaCache[$schema] ??= $this->findSchema($schema);
    }

    public function validate(string $schemaName, $data): Contracts\JsonSchemaValidationResult
    {
        $schema = $this->getSchema($schemaName);

        $result = false;
        $exception = null;
        try {
            $schema->in(json_decode($data, false, 512, JSON_THROW_ON_ERROR));

            $result = true;
        } catch (InvalidValue|JsonException $e) {
            $exception = $e;
        }

        return (new ValidationResult($result, $exception))->withContext($schemaName, $schema, $data);
    }
}