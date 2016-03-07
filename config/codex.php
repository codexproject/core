<?php


return [



    /*
    |--------------------------------------------------------------------------
    | Display name
    |--------------------------------------------------------------------------
    |
    */
    'display_name'                => env('CODEX_DISPLAY_NAME', 'Codex'),

    /*
    |--------------------------------------------------------------------------
    | Root directory
    |--------------------------------------------------------------------------
    |
    */
    'root_dir'                    => env('CODEX_ROOT_DIR', base_path('resources/docs')),

    /*
    |--------------------------------------------------------------------------
    | Route Base
    |--------------------------------------------------------------------------
    |
    | You may define a base route for your Codex documentation here. By default
    | it is set to "codex", but you may leave this empty if you wish to use
    | Codex as a stand alone application.
    |
    */
    'base_route'                  => env('CODEX_BASE_ROUTE', 'codex'),

    'routing' => [
        'base_route' => env('CODEX_BASE_ROUTE', 'codex'),
        'ignore_project_names' => [
            '_debugbar'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Project
    |--------------------------------------------------------------------------
    |
    */
    'default_project'             => env('CODEX_DEFAULT_PROJECT', 'codex'),

    /*
    |--------------------------------------------------------------------------
    | Default Project Attributes
    |--------------------------------------------------------------------------
    |
    | These values will be merged with any frontmatter attributes your
    | documentation pages may have. Feel free to add or remove any
    | attributes as you see fit for your documentation needs.
    |
    */
    'default_document_attributes' => [
        'author'   => 'John Doe',
        'title'    => 'Documentation',
        'subtitle' => '',
        'layout'   => 'codex::layouts.document',
        'view'     => 'codex::document',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Project Configuration
    |--------------------------------------------------------------------------
    |
    | These are the default settings used to pre-populate all project
    | configuration files.
    |
    */
    'default_project_config'      => [
        'description' => '',
        'default'     => \Codex\Core\Projects\Project::SHOW_LAST_VERSION_OTHERWISE_MASTER_BRANCH,
        'custom'      => null,
        'filters'     => [
            'enabled'      => [ 'front_matter', 'parsedown' ],
            'front_matter' => [ ],
            'parsedown'    => [
                'fenced_code_lang_class' => 'hljs lang-{LANG}'//'prettyprint lang-{LANG}'
            ],
        ],
        'hooks'       => [
            'enabled' => [ ],
            // '{hookName}' => [ hookSettings ]
        ],
    ],

    'log' => [
        'path' => storage_path('logs/codex.log'),
    ],

    'debug' => env('CODEX_DEBUG', false),

    'stubs_path' => __DIR__ . '/../resources/stubs',
];
