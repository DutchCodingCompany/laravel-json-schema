<?php

namespace DutchCodingCompany\LaravelJsonSchema\Contracts;

use Swaggest\JsonSchema\SchemaContract;
use Throwable;

interface JsonSchemaValidationResult
{
    public function passed(): bool;

    public function failed(): bool;

    public function getMessage(): ?string;

    public function getException(): ?Throwable;

    public function withContext(string $schemaName, SchemaContract $schema, $data = null): self;

    public function getSchemaName(): ?string;

    public function getSchema(): ?SchemaContract;

    public function getData();
}
