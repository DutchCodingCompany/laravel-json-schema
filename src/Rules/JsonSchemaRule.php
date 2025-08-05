<?php

namespace DutchCodingCompany\LaravelJsonSchema\Rules;

use DutchCodingCompany\LaravelJsonSchema\Contracts\JsonSchemaValidationResult;
use DutchCodingCompany\LaravelJsonSchema\JsonSchemaRepository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Rule;

class JsonSchemaRule implements Rule
{
    protected JsonSchemaRepository $repository;

    protected ?string $schema;

    protected bool $detailedMessage;

    protected ?JsonSchemaValidationResult $result = null;

    /**
     * @param string|null $schema schema name to validate
     */
    public function __construct(string | null $schema = null, bool $detailedMessage = true)
    {
        $this->repository = Container::getInstance()->make(JsonSchemaRepository::class);
        $this->schema = $schema;
        $this->detailedMessage = $detailedMessage;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->result = $this->repository->validate($this->schema, $value);

        return $this->result->passed();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $message = 'JSON validation failed for :attribute.';

        if ($this->detailedMessage) {
            $message .= ' '.(optional($this->result)->getMessage() ?? '');
        }

        return $message;
    }
}
