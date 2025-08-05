<?php

namespace DutchCodingCompany\LaravelJsonSchema\Rules;

use Closure;
use DutchCodingCompany\LaravelJsonSchema\Contracts\JsonSchemaValidator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\ValidationRule;

class JsonSchemaRule implements ValidationRule
{
    protected JsonSchemaValidator $schemaValidator;

    /**
     * @param  string  $schema  name of the schema to validate
     * @param  bool  $detailedMessage  wether of not to include the details of what failed
     * @param  \DutchCodingCompany\LaravelJsonSchema\Contracts\JsonSchemaValidator|null  $schemaValidator  custom repository, otherwise resolved from the service container
     */
    public function __construct(
        protected string $schema,
        protected bool $detailedMessage = true,
        JsonSchemaValidator | null $schemaValidator = null,
    ) {
        $this->schemaValidator = $schemaValidator ?? Container::getInstance()->make(JsonSchemaValidator::class);
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = $this->schemaValidator->validate($this->schema, $value);

        if ($result->failed()) {
            $fail($this->detailedMessage
                ? 'json-schema::messages.detailed-error-message'
                : 'json-schema::messages.error-message'
            )->translate([
                'attribute' => $attribute,
                'details' => $result->getMessage() ?? '',
            ]);
        }
    }
}
