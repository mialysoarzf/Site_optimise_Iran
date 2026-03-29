<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class DashboardRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function counters(): array
    {
        $sql = "
            SELECT
                (SELECT COUNT(*) FROM articles) AS total_articles,
                (SELECT COUNT(*) FROM articles WHERE status = 'draft') AS drafts,
                (SELECT COUNT(*) FROM articles WHERE status = 'published') AS published,
                (SELECT COUNT(*) FROM categories) AS categories
        ";

        return $this->pdo->query($sql)->fetch() ?: [
            'total_articles' => 0,
            'drafts' => 0,
            'published' => 0,
            'categories' => 0,
        ];
    }

    public function latestArticles(int $limit = 8): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.id, a.title, a.slug, a.status, a.updated_at, c.name AS category_name
             FROM articles a
             LEFT JOIN categories c ON c.id = a.category_id
             ORDER BY a.updated_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }
}
