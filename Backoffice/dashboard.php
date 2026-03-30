<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (!bo_auth_check()) {
    header('Location: /login');
    exit;
}

$pdo = bo_db($config['db']);
bo_dashboard($pdo);
