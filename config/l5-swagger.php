<?php

/*
|--------------------------------------------------------------------------
| L5-Swagger (NelmioApiDoc equivalent for Laravel)
|--------------------------------------------------------------------------
| Swagger UI available at:  GET /api/documentation
| OpenAPI JSON spec at:     GET /api/documentation.json
*/

return [
    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Book Library API',
            ],
            'routes' => [
                // URL of the Swagger UI page
                'docs' => 'api/doc',
            ],
            'paths' => [
                // Where the generated JSON spec is stored
                'docs'        => storage_path('api-docs'),
                'docs_json'   => 'api-docs.json',
                'docs_yaml'   => 'api-docs.yaml',
                // Where to scan for @OA annotations
                'annotations' => [base_path('app')],
                'excludes'    => [],
            ],
        ],
    ],

    'defaults' => [
        'routes' => [
            'docs'             => 'api/doc',
            'oauth2_callback'  => 'api/oauth2-callback',
            'middleware'       => ['api' => [], 'asset' => [], 'docs' => [], 'oauth2_callback' => []],
            'groupOptions'     => [],
        ],

        'paths' => [
            'docs'                   => storage_path('api-docs'),
            'docs_json'              => 'api-docs.json',
            'docs_yaml'              => 'api-docs.yaml',
            'annotations'            => [base_path('app')],
            'base'                   => null,
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
            'excludes'               => [],
        ],

        'scanOptions' => [
            'default_processors_configuration' => [],
            'analyser'   => null,
            'analysis'   => null,
            'processors' => [],
            'pattern'    => null,
            'exclude'    => [],
            'open_api_spec_version' => env('L5_SWAGGER_OPEN_API_SPEC_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        'securityDefinitions' => [
            'securitySchemes' => [],
            'security'        => [],
        ],

        'generate_always'      => env('L5_SWAGGER_GENERATE_ALWAYS', false),
        'generate_yaml_copy'   => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
        'proxy'                => false,
        'additional_config_url' => null,
        'operations_sort'      => env('L5_SWAGGER_OPERATIONS_SORT', null),
        'validator_url'        => null,

        'ui' => [
            'display' => [
                'dark_mode'    => env('L5_SWAGGER_UI_DARK_MODE', false),
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
                'filter'       => env('L5_SWAGGER_UI_FILTERS', true),
            ],
            'authorization' => [
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),
                'oauth2' => [
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost:8080'),
        ],
    ],
];
