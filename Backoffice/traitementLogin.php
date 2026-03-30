<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: /login');
    exit;
}

$pdo = bo_db($config['db']);
bo_login($pdo);
