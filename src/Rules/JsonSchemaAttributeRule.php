<?php

namespace DutchCodingCompany\LaravelJsonSchema\Rules;

use DutchCodingCompany\LaravelJsonSchema\Contracts\JsonSchemaNameProvider;
use DutchCodingCompany\LaravelJsonSchema\Contracts\JsonSchemaValidator;
use Illuminate\Contracts\Validation\DataAwareRule;
use InvalidArgumentException;

class JsonSchemaAttributeRule extends BaseRule implements DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * @param  string  $attribute  from with other attribute to retrieve the schema name
     * @param  class-string<\BackedEnum&\DutchCodingCompany\LaravelJsonSchema\Contracts\JsonSchemaNameProvider>|null  $enum  which enum to cast the schema name into (if applicable)
     */
    public function __construct(
        protected string $attribute,
        protected ?string $enum = null,
        protected bool $detailedMessage = true,
        JsonSchemaValidator | null $schemaValidator = null,
        protected bool $decode = true,
    ) {
        parent::__construct($detailedMessage, $schemaValidator, $decode);
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    protected function determineSchemaName(): string
    {
        $schemaName = data_get($this->data, $this->attribute);

        if ($this->enum !== null) {
            $enum = ($this->enum)::tryFrom($schemaName);

            if ($enum instanceof JsonSchemaNameProvider) {
                return $enum->getSchemaName();
            }
        }

        if (is_string($schemaName)) {
            return $schemaName;
        }

        throw new InvalidArgumentException('Cannot determine schema name from attribute "'.$this->attribute.'"');
    }
}
