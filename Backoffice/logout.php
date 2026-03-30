<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: /dashboard');
    exit;
}

if (!bo_auth_check()) {
    header('Location: /login');
    exit;
}

bo_logout();
