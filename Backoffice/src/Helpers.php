<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function method_is(string $method): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === strtoupper($method);
}

function path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';

    return rtrim($path, '/') ?: '/';
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][$type][] = $message;
}

function get_flash(): array
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $flash;
}

function old(string $key, string $default = ''): string
{
    return $_SESSION['old'][$key] ?? $default;
}

function with_old(array $data): void
{
    $_SESSION['old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function view(string $template, array $params = []): void
{
    $templatePath = __DIR__ . '/../templates/' . $template . '.php';
    if (!is_file($templatePath)) {
        http_response_code(500);
        exit('Template introuvable');
    }

    extract($params, EXTR_SKIP);
    require __DIR__ . '/../templates/layout.php';
}

function truncate(string $value, int $length = 150): string
{
    if (mb_strlen($value) <= $length) {
        return $value;
    }

    return mb_substr($value, 0, $length - 1) . '…';
}
