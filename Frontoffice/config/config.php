<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Iran Infos',
        'base_url' => getenv('APP_BASE_URL_FRONT') ?: '',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'port' => getenv('DB_PORT') ?: '5432',
        'name' => getenv('DB_NAME') ?: 'app_db',
        'user' => getenv('DB_USER') ?: 'app_user',
        'pass' => getenv('DB_PASSWORD') ?: 'app_password',
    ],
];
