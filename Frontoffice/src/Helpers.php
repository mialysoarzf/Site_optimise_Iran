<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';

    return rtrim($path, '/') ?: '/';
}

function query_page(): int
{
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if (!$page || $page < 1) {
        return 1;
    }

    return $page;
}

function excerpt_from_content(string $content, int $length = 180): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? '');
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length - 1) . '…';
}

function view(string $template, array $params = []): void
{
    $templatePath = __DIR__ . '/../templates/' . $template . '.php';
    if (!is_file($templatePath)) {
        http_response_code(500);
        exit('Template introuvable');
    }

    if (!array_key_exists('tickerItems', $params) && isset($GLOBALS['fo_ticker_items']) && is_array($GLOBALS['fo_ticker_items'])) {
        $params['tickerItems'] = $GLOBALS['fo_ticker_items'];
    }

    extract($params, EXTR_SKIP);
    require __DIR__ . '/../templates/layout.php';
}

function url_with_page(string $basePath, int $page): string
{
    return $page <= 1 ? $basePath : $basePath . '?page=' . $page;
}

function valid_slug(string $slug): bool
{
    return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
}

function article_meta_description(array $article): string
{
    $meta = trim((string) ($article['meta_description'] ?? ''));
    if ($meta !== '') {
        return $meta;
    }

    $excerpt = trim((string) ($article['excerpt'] ?? ''));
    if ($excerpt !== '') {
        return excerpt_from_content($excerpt, 160);
    }

    return excerpt_from_content((string) ($article['content'] ?? ''), 160);
}

function media_url(?string $path): string
{
    $value = trim((string) $path);
    if ($value === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $value)) {
        return $value;
    }

    $normalized = '/' . ltrim($value, '/');
    if (str_starts_with($normalized, '/uploads/')) {
        $base = rtrim((string) (getenv('APP_BASE_URL_BACKOFFICE') ?: 'http://localhost:8081'), '/');
        return $base . $normalized;
    }

    return $normalized;
}

function safe_alt(?string $alt, string $fallback = 'Illustration'): string
{
    $value = trim((string) $alt);
    if ($value === '' || str_contains($value, '??')) {
        return $fallback;
    }

    return $value;
}
