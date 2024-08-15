# Json Schema
[![Latest Version on Packagist](https://img.shields.io/packagist/v/dutchcodingcompany/laravel-json-schema.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/laravel-json-schema)
[![Total Downloads](https://img.shields.io/packagist/dt/dutchcodingcompany/laravel-json-schema.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/laravel-json-schema)

This package makes it easy to use `swaggest/json-schema` in laravel.

## Installation

You can install the package via composer:

```bash
composer require dutchcodingcompany/laravel-json-schema
```

## Usage
1. Create a json schema in the schema directory, eg. `storage/app/schema/example.json`
2. Reference the schema in the validator: `new \DutchCodingCompany\LaravelJsonSchema\Rules\JsonSchemaRule('example')`

To customize the schema directory, use environment variable `JSON_SCHEMA_DIRECTORY` or publish the config file.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
