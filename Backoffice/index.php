<?php

declare(strict_types=1);

use App\Auth;
use App\Controllers\AdminController;
use App\Controllers\ArticleController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Database;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\DashboardRepository;
use App\Repositories\ImageRepository;
use App\Repositories\UserRepository;

require __DIR__ . '/bootstrap.php';

$pdo = Database::connection($config['db']);

$users = new UserRepository($pdo);
$auth = new Auth($users);
$dashboardController = new AdminController(new DashboardRepository($pdo));
$authController = new AuthController($auth);
$categoryController = new CategoryController(new CategoryRepository($pdo));
$articleController = new ArticleController(
    new ArticleRepository($pdo),
    new CategoryRepository($pdo),
    new ImageRepository($pdo),
    $config
);

$currentPath = path();

$publicRoutes = [
    '/admin/login',
];

if (!$auth->check() && !in_array($currentPath, $publicRoutes, true)) {
    redirect('/admin/login');
}

if ($auth->check() && $currentPath === '/admin/login' && method_is('GET')) {
    redirect('/admin/dashboard');
}

if ($currentPath === '/') {
    redirect($auth->check() ? '/admin/dashboard' : '/admin/login');
}

$matched = false;

if ($currentPath === '/admin/login' && method_is('GET')) {
    $matched = true;
    $authController->loginForm();
}

if ($currentPath === '/admin/login' && method_is('POST')) {
    $matched = true;
    $authController->login();
}

if ($currentPath === '/admin/logout' && method_is('POST')) {
    $matched = true;
    $authController->logout();
}

if ($currentPath === '/admin/dashboard' && method_is('GET')) {
    $matched = true;
    $dashboardController->dashboard();
}

if ($currentPath === '/admin/categories' && method_is('GET')) {
    $matched = true;
    $categoryController->index();
}

if ($currentPath === '/admin/categories/create' && method_is('GET')) {
    $matched = true;
    $categoryController->createForm();
}

if ($currentPath === '/admin/categories/create' && method_is('POST')) {
    $matched = true;
    $categoryController->create();
}

if (preg_match('#^/admin/categories/edit/(\d+)$#', $currentPath, $m) && method_is('GET')) {
    $matched = true;
    $categoryController->editForm((int) $m[1]);
}

if (preg_match('#^/admin/categories/edit/(\d+)$#', $currentPath, $m) && method_is('POST')) {
    $matched = true;
    $categoryController->update((int) $m[1]);
}

if (preg_match('#^/admin/categories/delete/(\d+)$#', $currentPath, $m) && method_is('POST')) {
    $matched = true;
    $categoryController->delete((int) $m[1]);
}

if ($currentPath === '/admin/articles' && method_is('GET')) {
    $matched = true;
    $articleController->index();
}

if ($currentPath === '/admin/articles/create' && method_is('GET')) {
    $matched = true;
    $articleController->createForm();
}

if ($currentPath === '/admin/articles/create' && method_is('POST')) {
    $matched = true;
    $articleController->create($auth->id() ?? 1);
}

if ($currentPath === '/admin/articles/editor-upload' && method_is('POST')) {
    $matched = true;
    $articleController->uploadEditorImage();
}

if (preg_match('#^/admin/articles/edit/(\d+)$#', $currentPath, $m) && method_is('GET')) {
    $matched = true;
    $articleController->editForm((int) $m[1]);
}

if (preg_match('#^/admin/articles/edit/(\d+)$#', $currentPath, $m) && method_is('POST')) {
    $matched = true;
    $articleController->update((int) $m[1]);
}

if (preg_match('#^/admin/articles/delete/(\d+)$#', $currentPath, $m) && method_is('POST')) {
    $matched = true;
    $articleController->delete((int) $m[1]);
}

if (!$matched) {
    http_response_code(404);
    view('admin/404', [
        'pageTitle' => '404 - Page introuvable',
        'metaDescription' => 'Route introuvable',
    ]);
}
