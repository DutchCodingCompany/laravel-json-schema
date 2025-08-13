<?php

namespace DutchCodingCompany\LaravelJsonSchema\Contracts;

interface JsonSchemaValidator
{
    public function validate(string $schemaName, mixed $data): JsonSchemaValidationResult;
}
