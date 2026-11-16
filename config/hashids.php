<?php

declare(strict_types=1);

$connections = [
    'main' => [
        'salt' => env('HASHIDS_SALT', env('APP_KEY', '')),
        'length' => 5,
    ],
];

// App\Traits\Hashidable calls Hashids::connection(<model class>), so every model in
// app/Models (including sub-folders) gets its own connection named after its class.
$modelsPath = app_path('Models');

if (is_dir($modelsPath)) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($modelsPath, FilesystemIterator::SKIP_DOTS));

    foreach ($files as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $relative = substr($file->getPathname(), strlen($modelsPath) + 1, -4);
        $class = 'App\\Models\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);

        $connections[$class] = [
            'salt' => $class.md5($class.env('HASHIDS_SALT', env('APP_KEY', ''))),
            'length' => 13,
        ];
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    */

    'connections' => $connections,

];
