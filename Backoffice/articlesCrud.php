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
    bo_articles_index($pdo);
    exit;
}

if ($action === 'create' && $method === 'GET') {
    bo_articles_create_form($pdo, $config);
    exit;
}

if ($action === 'create' && $method === 'POST') {
    bo_articles_create_action($pdo, $config, bo_auth_id() ?? 1);
    exit;
}

if ($action === 'edit' && $id > 0 && $method === 'GET') {
    bo_articles_edit_form($pdo, $config, $id);
    exit;
}

if ($action === 'edit' && $id > 0 && $method === 'POST') {
    bo_articles_update_action($pdo, $config, $id);
    exit;
}

if ($action === 'delete' && $id > 0 && $method === 'POST') {
    bo_articles_delete_action($pdo, $id);
    exit;
}

if ($action === 'editor-upload' && $method === 'POST') {
    bo_articles_editor_upload($config);
    exit;
}

http_response_code(404);
view('admin/404', [
    'pageTitle' => '404 - Page introuvable',
    'metaDescription' => 'Route article introuvable',
]);
