<?php

namespace DutchCodingCompany\LaravelJsonSchema\Contracts;

use Throwable;
use Swaggest\JsonSchema\SchemaContract;

interface JsonSchemaValidationResult
{
    public function passed(): bool;
    public function failed(): bool;

    public function getMessage(): ?string;
    public function getException(): ?Throwable;

    public function withContext(string $schemaName, SchemaContract $schema, $data = null): JsonSchemaValidationResult;

    public function getSchemaName(): ?string;
    public function getSchema(): ?SchemaContract;
    public function getData();
}