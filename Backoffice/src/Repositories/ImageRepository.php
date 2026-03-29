<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ImageRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function byArticle(int $articleId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, article_id, url, alt FROM images WHERE article_id = :article_id LIMIT 1');
        $stmt->execute(['article_id' => $articleId]);

        return $stmt->fetch() ?: null;
    }

    public function upsert(int $articleId, string $url, string $alt): void
    {
        $existing = $this->byArticle($articleId);
        if ($existing) {
            $stmt = $this->pdo->prepare('UPDATE images SET url = :url, alt = :alt WHERE id = :id');
            $stmt->execute([
                'id' => $existing['id'],
                'url' => $url,
                'alt' => $alt,
            ]);

            return;
        }

        $stmt = $this->pdo->prepare('INSERT INTO images (article_id, url, alt) VALUES (:article_id, :url, :alt)');
        $stmt->execute([
            'article_id' => $articleId,
            'url' => $url,
            'alt' => $alt,
        ]);
    }

    public function deleteByArticle(int $articleId): ?array
    {
        $image = $this->byArticle($articleId);
        if (!$image) {
            return null;
        }

        $stmt = $this->pdo->prepare('DELETE FROM images WHERE article_id = :article_id');
        $stmt->execute(['article_id' => $articleId]);

        return $image;
    }
}
