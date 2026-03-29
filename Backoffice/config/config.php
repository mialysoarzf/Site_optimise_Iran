<?php

declare(strict_types=1);

return [
    'app' => [
        'env' => getenv('APP_ENV') ?: 'prod',
        'name' => 'Iran War - Backoffice',
        'base_url' => getenv('APP_BASE_URL') ?: '',
        'upload_dir' => __DIR__ . '/../uploads',
        'max_upload_size' => 2 * 1024 * 1024,
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'port' => getenv('DB_PORT') ?: '5432',
        'name' => getenv('DB_NAME') ?: 'app_db',
        'user' => getenv('DB_USER') ?: 'app_user',
        'pass' => getenv('DB_PASSWORD') ?: 'app_password',
    ],
];
