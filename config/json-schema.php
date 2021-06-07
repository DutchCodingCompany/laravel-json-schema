<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Disk and path where schema's should be stored
    |--------------------------------------------------------------------------
    |
    | Defaults to filesystems.default
    | Uses schema folder by default
    */
    'disk' => env('JSON_SCHEMA_DISK', null),

    'directory' => env('JSON_SCHEMA_DIRECTORY', 'schema'),

    /*
    |--------------------------------------------------------------------------
    | Filter for file match
    |--------------------------------------------------------------------------
    |
    | Used in searching for files
    |
    | Filter is `pattern` format for `preg_match`, default pattern is anything ending in .json
    */
    'file-search' => [
        'filter' => ',(.*)\.json,i', // false or null for no filter
        'schema-name' => '$1', // what part is used as schema name
        'recursive' => false,
    ],
];