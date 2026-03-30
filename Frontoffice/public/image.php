<?php

declare(strict_types=1);

$src = trim((string) ($_GET['src'] ?? ''));
$width = (int) ($_GET['w'] ?? 960);
$quality = (int) ($_GET['q'] ?? 72);

$width = max(160, min(2200, $width));
$quality = max(45, min(90, $quality));

if ($src === '') {
    http_response_code(400);
    exit('Missing src');
}

function resolve_source_url(string $src): ?string
{
    if (preg_match('#^https?://#i', $src)) {
        $parts = parse_url($src);
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $path = (string) ($parts['path'] ?? '');
        if (!str_starts_with($path, '/uploads/')) {
            return null;
        }

        $host = strtolower((string) $parts['host']);
        if ($host === 'localhost' || $host === '127.0.0.1') {
            $internalBase = rtrim((string) (getenv('APP_BASE_URL_BACKOFFICE_INTERNAL') ?: 'http://backoffice'), '/');
            return $internalBase . $path;
        }

        return $src;
    }

    $normalized = '/' . ltrim($src, '/');
    if (!str_starts_with($normalized, '/uploads/')) {
        return null;
    }

    $internalBase = rtrim((string) (getenv('APP_BASE_URL_BACKOFFICE_INTERNAL') ?: 'http://backoffice'), '/');
    return $internalBase . $normalized;
}

$sourceUrl = resolve_source_url($src);
if ($sourceUrl === null) {
    http_response_code(400);
    exit('Invalid src');
}

$cacheDir = __DIR__ . '/cache/images';
if (!is_dir($cacheDir) && !@mkdir($cacheDir, 0775, true) && !is_dir($cacheDir)) {
    http_response_code(500);
    exit('Cache directory error');
}

$cacheKey = hash('sha256', $sourceUrl . '|' . $width . '|' . $quality);
$cacheFile = $cacheDir . '/' . $cacheKey . '.webp';

if (is_file($cacheFile) && filesize($cacheFile) > 0) {
    header('Content-Type: image/webp');
    header('Cache-Control: public, max-age=2592000, immutable');
    readfile($cacheFile);
    exit;
}

$context = stream_context_create([
    'http' => [
        'timeout' => 8,
        'follow_location' => 1,
        'ignore_errors' => true,
    ],
]);

$raw = @file_get_contents($sourceUrl, false, $context);
if ($raw === false || $raw === '') {
    http_response_code(502);
    exit('Image fetch failed');
}

$img = @imagecreatefromstring($raw);
if ($img === false) {
    http_response_code(415);
    exit('Unsupported image');
}

$origW = imagesx($img);
$origH = imagesy($img);

if ($origW <= 0 || $origH <= 0) {
    imagedestroy($img);
    http_response_code(415);
    exit('Invalid image');
}

$targetW = min($width, $origW);
$targetH = (int) round(($origH / $origW) * $targetW);
$targetH = max(1, $targetH);

$canvas = imagecreatetruecolor($targetW, $targetH);
imagealphablending($canvas, true);
imagesavealpha($canvas, true);
imagecopyresampled($canvas, $img, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

if (!imagewebp($canvas, $cacheFile, $quality)) {
    imagedestroy($canvas);
    imagedestroy($img);
    http_response_code(500);
    exit('Image encode failed');
}

imagedestroy($canvas);
imagedestroy($img);

header('Content-Type: image/webp');
header('Cache-Control: public, max-age=2592000, immutable');
readfile($cacheFile);
