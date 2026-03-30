<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (!bo_auth_check()) {
    header('Location: /login');
    exit;
}

$pdo = bo_db($config['db']);
$action = (string) ($_GET['action'] ?? 'index');
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$id = (int) ($_GET['id'] ?? 0);

if ($action === 'index' && $method === 'GET') {
    bo_categories_index($pdo);
    exit;
}

if ($action === 'create' && $method === 'GET') {
    bo_categories_create_form();
    exit;
}

if ($action === 'create' && $method === 'POST') {
    bo_categories_create_action($pdo);
    exit;
}

if ($action === 'edit' && $id > 0 && $method === 'GET') {
    bo_categories_edit_form($pdo, $id);
    exit;
}

if ($action === 'edit' && $id > 0 && $method === 'POST') {
    bo_categories_update_action($pdo, $id);
    exit;
}

if ($action === 'delete' && $id > 0 && $method === 'POST') {
    bo_categories_delete_action($pdo, $id);
    exit;
}

http_response_code(404);
view('admin/404', [
    'pageTitle' => '404 - Page introuvable',
    'metaDescription' => 'Route catégorie introuvable',
]);
