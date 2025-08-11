<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel server requirements, you can add as many
    | as your application require, we check if the extension is enabled
    | by looping through the array and run "extension_loaded" on it.
    |
    */
    'core' => [
        'minPhpVersion' => '8.2',
    ],
    'final' => [
        'key' => true,
        'publish' => false,
    ],
    'requirements' => [
        'php' => [
            'openssl',
            'pdo',
            'mbstring',
            'tokenizer',
            'JSON',
            'cURL',
        ],
        'apache' => [
            'mod_rewrite',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Folders Permissions
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel folders permissions, if your application
    | requires more permissions just add them to the array list bellow.
    |
    */
    'permissions' => [
        'storage/framework/'     => '775',
        'storage/logs/'          => '775',
        'bootstrap/cache/'       => '775',
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Form Wizard Validation Rules & Messages
    |--------------------------------------------------------------------------
    |
    | This are the default form field validation rules. Available Rules:
    | https://laravel.com/docs/5.4/validation#available-validation-rules
    |
    */
    'environment' => [
        'form' => [
            'rules' => [
                'app_name'              => 'required|string|max:50',
                'environment'           => 'required|string|max:50',
                'environment_custom'    => 'required_if:environment,other|max:50',
                'app_url'               => 'required|url',
                'database_connection'   => 'required|string|max:50',
                'database_hostname'     => 'required|string|max:50',
                'database_port'         => 'required|numeric',
                'database_name'         => 'required|string|max:50',
                'database_username'     => 'required|string|max:50',
                'database_password'     => 'nullable|string|max:50',
                'admin_name'            => 'required|string|max:50',
                'admin_email'           => 'required|email|max:50',
                'admin_password'        => 'required|string|min:6|max:50',
                'admin_password'        => 'required|string|min:6|max:50|confirmed',
            ],
            'messages' => [
                'app_name.required'             => 'Application name is required.',
                'app_name.string'               => 'Application name must be a text.',
                'database_connection.required'  => 'Please select a database driver.',
                'database_hostname.required'    => 'Please enter the database host.',
                'database_port.required'        => 'Database port is required.',
                'database_name.required'        => 'Database name is required.',
                'database_username.required'    => 'Database username is required.',
                'admin_name.required'           => 'Please enter the admin name.',
                'admin_email.required'          => 'Please enter the admin email.',
                'admin_email.email'             => 'Please enter a valid email address.',
                'admin_password.required'       => 'Please enter the admin password.',
                'admin_password.confirmed'      => 'Password confirmation does not match.',
                'admin_password.min'            => 'Password must be at least 6 characters.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Installed Middleware Options
    |--------------------------------------------------------------------------
    | Different available status switch configuration for the
    | canInstall middleware located in `CanInstall.php`.
    |
    */
    'installed' => [
        'redirectOptions' => [
            'route' => [
                'name' => 'welcome',
                'data' => [],
            ],
            'abort' => [
                'type' => '404',
            ],
            'dump' => [
                'data' => 'Dumping a not found message.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Selected Installed Middleware Option
    |--------------------------------------------------------------------------
    | The selected option fo what happens when an installer instance has been
    | Default output is to `/resources/views/error/404.blade.php` if none.
    | The available middleware options include:
    | route, abort, dump, 404, default, ''
    |
    */
    'installedAlreadyAction' => '',

    /*
    |--------------------------------------------------------------------------
    | Updater Enabled
    |--------------------------------------------------------------------------
    | Can the application run the '/update' route with the migrations.
    | The default option is set to False if none is present.
    | Boolean value
    |
    */
    'updaterEnabled' => 'true',

    'site_url' => env('CODESHAPER_SITE_URL', 'https://codeshaper.net'),

    'is_environment_wizard_enabled' => env('CODESHAPER_ENVIRONMENT_WIZARD_ENABLED', true),

    'item_id' => env('CODESHAPER_ITEM_ID'),

];
