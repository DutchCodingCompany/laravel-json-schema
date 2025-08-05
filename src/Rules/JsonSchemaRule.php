<?php

namespace DutchCodingCompany\LaravelJsonSchema\Rules;

use Closure;
use DutchCodingCompany\LaravelJsonSchema\Contracts\JsonSchemaValidator;

class JsonSchemaRule extends BaseRule
{
    /**
     * @param  string  $schema  name of the schema to validate
     */
    public function __construct(
        protected string $schema,
        protected bool $detailedMessage = true,
        JsonSchemaValidator | null $schemaValidator = null,
    ) {
        parent::__construct($detailedMessage, $schemaValidator);
    }

    protected function determineSchemaName(): string
    {
        return $this->schema;
    }
}
