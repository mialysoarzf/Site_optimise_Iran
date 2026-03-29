<?php

declare(strict_types=1);

use App\Controllers\FrontController;
use App\Database;
use App\Repositories\FrontRepository;

require __DIR__ . '/bootstrap.php';

$pdo = Database::connection($config['db']);
$controller = new FrontController(new FrontRepository($pdo));

$currentPath = path();

if ($currentPath === '/') {
    $controller->home();
    return;
}

if ($currentPath === '/articles') {
    $controller->articles();
    return;
}

if ($currentPath === '/a-propos') {
    $controller->about();
    return;
}

if (preg_match('#^/article/([a-z0-9-]+)$#', $currentPath, $match)) {
    $slug = $match[1];
    if (!valid_slug($slug)) {
        $controller->notFound();
        return;
    }

    $controller->article($slug);
    return;
}

if (preg_match('#^/categorie/([a-z0-9-]+)$#', $currentPath, $match)) {
    $slug = $match[1];
    if (!valid_slug($slug)) {
        $controller->notFound();
        return;
    }

    $controller->category($slug);
    return;
}

$controller->notFound();
