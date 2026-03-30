<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$pdo = bo_db($config['db']);

$currentPath = path();

$publicRoutes = [
    '/admin/login',
];

if (!bo_auth_check() && !in_array($currentPath, $publicRoutes, true)) {
    redirect('/admin/login');
}

if (bo_auth_check() && $currentPath === '/admin/login' && method_is('GET')) {
    redirect('/admin/dashboard');
}

if ($currentPath === '/') {
    redirect(bo_auth_check() ? '/admin/dashboard' : '/admin/login');
}

$matched = false;

if ($currentPath === '/admin/login' && method_is('GET')) {
    $matched = true;
    bo_show_login();
}

if ($currentPath === '/admin/login' && method_is('POST')) {
    $matched = true;
    bo_login($pdo);
}

if ($currentPath === '/admin/logout' && method_is('POST')) {
    $matched = true;
    bo_logout();
}

if ($currentPath === '/admin/dashboard' && method_is('GET')) {
    $matched = true;
    bo_dashboard($pdo);
}

if ($currentPath === '/admin/categories' && method_is('GET')) {
    $matched = true;
    bo_categories_index($pdo);
}

if ($currentPath === '/admin/categories/create' && method_is('GET')) {
    $matched = true;
    bo_categories_create_form();
}

if ($currentPath === '/admin/categories/create' && method_is('POST')) {
    $matched = true;
    bo_categories_create_action($pdo);
}

if (preg_match('#^/admin/categories/edit/(\d+)$#', $currentPath, $m) && method_is('GET')) {
    $matched = true;
    bo_categories_edit_form($pdo, (int) $m[1]);
}

if (preg_match('#^/admin/categories/edit/(\d+)$#', $currentPath, $m) && method_is('POST')) {
    $matched = true;
    bo_categories_update_action($pdo, (int) $m[1]);
}

if (preg_match('#^/admin/categories/delete/(\d+)$#', $currentPath, $m) && method_is('POST')) {
    $matched = true;
    bo_categories_delete_action($pdo, (int) $m[1]);
}

if ($currentPath === '/admin/articles' && method_is('GET')) {
    $matched = true;
    bo_articles_index($pdo);
}

if ($currentPath === '/admin/articles/create' && method_is('GET')) {
    $matched = true;
    bo_articles_create_form($pdo, $config);
}

if ($currentPath === '/admin/articles/create' && method_is('POST')) {
    $matched = true;
    bo_articles_create_action($pdo, $config, bo_auth_id() ?? 1);
}

if ($currentPath === '/admin/articles/editor-upload' && method_is('POST')) {
    $matched = true;
    bo_articles_editor_upload($config);
}

if (preg_match('#^/admin/articles/edit/(\d+)$#', $currentPath, $m) && method_is('GET')) {
    $matched = true;
    bo_articles_edit_form($pdo, $config, (int) $m[1]);
}

if (preg_match('#^/admin/articles/edit/(\d+)$#', $currentPath, $m) && method_is('POST')) {
    $matched = true;
    bo_articles_update_action($pdo, $config, (int) $m[1]);
}

if (preg_match('#^/admin/articles/delete/(\d+)$#', $currentPath, $m) && method_is('POST')) {
    $matched = true;
    bo_articles_delete_action($pdo, (int) $m[1]);
}

if (!$matched) {
    http_response_code(404);
    view('admin/404', [
        'pageTitle' => '404 - Page introuvable',
        'metaDescription' => 'Route introuvable',
    ]);
}
