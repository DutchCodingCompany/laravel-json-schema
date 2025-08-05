<?php

namespace DutchCodingCompany\LaravelJsonSchema;

use Swaggest\JsonSchema\SchemaContract;
use Throwable;

class ValidationResult implements Contracts\JsonSchemaValidationResult
{
    public function __construct(
        protected bool $result,
        protected string $schemaName,
        protected ?SchemaContract $schema = null,
        protected ?string $data = null,
        protected ?Throwable $exception = null,
    ) {
        //
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
        return $this->exception?->getMessage();
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

    public function getData(): ?string
    {
        return $this->data;
    }
}
