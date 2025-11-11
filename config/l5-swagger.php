<?php

return [
    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                // Título que verás en la UI
                'title' => 'Reservas QR — Auth API',
            ],

            'routes' => [
                /*
                 * Ruta para acceder a la interfaz de Swagger UI.
                 * Quedará en: /api/documentation
                 */
                'api' => 'api/documentation',
            ],

            'paths' => [
                /*
                 * Usar rutas absolutas en los assets de la UI
                 */
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', false),

                /*
                 * Directorio de assets de Swagger UI
                 */
                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),

                /*
                 * Nombre del archivo JSON generado
                 */
                'docs_json' => 'api-docs.json',

                /*
                 * Nombre del archivo YAML generado
                 */
                'docs_yaml' => 'api-docs.yaml',

                /*
                 * Formato usado por la UI: json o yaml
                 */
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                /*
                 * Directorios con anotaciones @OA\... a escanear
                 * (ajustado para tu API)
                 */
                'annotations' => [
                    base_path('app/Http/Controllers/Api'),
                    base_path('app/OpenApi'),
                ],
            ],
        ],
    ],

    'defaults' => [
        'routes' => [
            /*
             * Ruta para servir el JSON parseado
             * (Por defecto era "docs"; lo movemos a /api/docs)
             */
            'docs' => 'api/docs',

            /*
             * Callback de oauth2 (no lo usas, lo dejamos por defecto)
             */
            'oauth2_callback' => 'api/oauth2-callback',

            /*
             * Middlewares para controlar acceso a UI, JSON y assets
             * (agrega aquí ['admin.only'] si quieres proteger la UI en prod)
             */
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],

            /*
             * Opciones del Route::group de los docs
             */
            'group_options' => [],
        ],

        'paths' => [
            /*
             * Carpeta donde se guardan los JSON/YAML generados
             */
            'docs' => storage_path('api-docs'),

            /*
             * Carpeta para exportar vistas
             */
            'views' => base_path('resources/views/vendor/l5-swagger'),

            /*
             * Base path de la API (opcional)
             */
            'base' => env('L5_SWAGGER_BASE_PATH', null),

            /*
             * Directorios a excluir del escaneo (usa scanOptions.exclude para sobrescribir)
             */
            'excludes' => [],
        ],

        'scanOptions' => [
            // Config de procesadores por defecto (normalmente no necesitas tocar esto)
            'default_processors_configuration' => [
                // Ejemplos en la doc de swagger-php
            ],

            'analyser' => null,
            'analysis' => null,

            // Procesadores custom opcionales
            'processors' => [
                // new \App\SwaggerProcessors\SchemaQueryParameter(),
            ],

            // Patrón de archivos a escanear (null = *.php)
            'pattern' => null,

            // Excluir rutas del escaneo (sobrescribe paths.excludes)
            'exclude' => [],

            // Versión de OpenAPI (3.0 por defecto)
            'open_api_spec_version' => env('L5_SWAGGER_OPEN_API_SPEC_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        /*
         * Definiciones de seguridad (útil si no declaras @OA\SecurityScheme en anotaciones)
         * Dejamos bearerAuth listo para usar con Sanctum (tokens personales).
         */
        'securityDefinitions' => [
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'apiKey',
                    'description' => 'Enter token in format: Bearer <token>',
                    'name' => 'Authorization',
                    'in' => 'header',
                ],
            ],
            'security' => [
                // Aplica bearerAuth por defecto (puedes quitarlo si prefieres setearlo por endpoint)
                [
                    'bearerAuth' => [],
                ],
            ],
        ],

        /*
         * En desarrollo, puedes regenerar siempre los docs en cada request.
         * Controla con .env: L5_SWAGGER_GENERATE_ALWAYS=true
         */
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true),

        /*
         * Generar copia YAML adicional
         */
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

        /*
         * Proxy (si estás detrás de LB/Proxy y necesitas confiar en IPs)
         */
        'proxy' => false,

        /*
         * Configs plugin (normalmente null)
         */
        'additional_config_url' => null,

        /*
         * Orden de operaciones en la UI: 'alpha' | 'method' | null
         */
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

        /*
         * URL del validador remoto (null para deshabilitar)
         */
        'validator_url' => null,

        /*
         * Configuración de la UI
         */
        'ui' => [
            'display' => [
                'dark_mode' => env('L5_SWAGGER_UI_DARK_MODE', false),
                // 'list' | 'full' | 'none'
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
                // Filtro en la barra superior
                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],
            'authorization' => [
                // Persistir autorización en el navegador
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),
                'oauth2' => [
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        /*
         * Constantes para usar en anotaciones (si las necesitas)
         */
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://127.0.0.1:8000'),
        ],
    ],
];
