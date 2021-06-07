<?php

namespace DutchCodingCompany\LaravelJsonSchema;

use Swaggest\JsonSchema\SchemaContract;
use Throwable;

class ValidationResult implements Contracts\JsonSchemaValidationResult
{
    protected bool $result;
    protected ?Throwable $exception;

    protected ?string $schemaName = null;
    protected ?SchemaContract $schema = null;
    protected $data = null;

    public function __construct(bool $result, ?Throwable $exception = null)
    {
        $this->result = $result;
        $this->exception = $exception;
    }

    public function withContext(string $schemaName, SchemaContract $schema, $data = null): self
    {
        $this->schemaName = $schemaName;
        $this->schema = $schema;
        $this->data = $data;

        return $this;
    }

    public function passed(): bool
    {
        return $this->result;
    }

    public function failed(): bool
    {
        return ! $this->passed();
    }

    public function getMessage(): ?string
    {
        return optional($this->exception)->getMessage();
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    public function getSchemaName(): ?string
    {
        return $this->schemaName;
    }

    public function getSchema(): ?SchemaContract
    {
        return $this->schema;
    }

    public function getData()
    {
        return $this->data;
    }
}