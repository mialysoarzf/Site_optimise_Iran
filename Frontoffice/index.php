<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$pdo = fo_db($config['db']);

$GLOBALS['fo_ticker_items'] = array_values(array_filter(array_map(
    static fn (array $row): array => [
        'title' => trim((string) ($row['title'] ?? '')),
        'slug' => trim((string) ($row['slug'] ?? '')),
    ],
    fo_latest_published($pdo, 8)
), static fn (array $item): bool => $item['title'] !== '' && $item['slug'] !== ''));

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$currentPath = parse_url($uri, PHP_URL_PATH) ?: '/';
$currentPath = rtrim($currentPath, '/') ?: '/';

if ($currentPath === '/') {
    fo_home($pdo);
    return;
}

if ($currentPath === '/articles') {
    fo_articles($pdo);
    return;
}

if ($currentPath === '/a-propos') {
    fo_about();
    return;
}

if (preg_match('#^/article/([a-z0-9-]+)$#', $currentPath, $match)) {
    $slug = $match[1];
    if (!valid_slug($slug)) {
        fo_not_found();
        return;
    }

    fo_article($pdo, $slug);
    return;
}

if (preg_match('#^/categorie/([a-z0-9-]+)$#', $currentPath, $match)) {
    $slug = $match[1];
    if (!valid_slug($slug)) {
        fo_not_found();
        return;
    }

    fo_category($pdo, $slug);
    return;
}

fo_not_found();
