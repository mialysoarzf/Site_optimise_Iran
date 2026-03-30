<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class FrontRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function latestPublished(int $limit = 6): array
    {
        $sql = '
            SELECT
                a.id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.meta_title,
                a.meta_description,
                a.published_at,
                a.created_at,
                a.updated_at,
                c.name AS category_name,
                c.slug AS category_slug,
                img.url AS image_url,
                img.alt AS image_alt
            FROM articles a
            LEFT JOIN categories c ON c.id = a.category_id
            LEFT JOIN LATERAL (
                SELECT i.url, i.alt
                FROM images i
                WHERE i.article_id = a.id
                ORDER BY i.created_at ASC
                LIMIT 1
            ) img ON true
            WHERE a.status = :status
            ORDER BY COALESCE(a.updated_at, a.created_at) DESC
            LIMIT :limit
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', 'published', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function categoriesWithPublishedCount(): array
    {
        $sql = '
            SELECT c.id, c.name, c.slug, COUNT(a.id) AS published_count
            FROM categories c
            LEFT JOIN articles a ON a.category_id = c.id AND a.status = :status
            GROUP BY c.id
            HAVING COUNT(a.id) > 0
            ORDER BY c.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['status' => 'published']);

        return $stmt->fetchAll() ?: [];
    }

    public function paginatePublished(int $page = 1, int $perPage = 10, ?string $categorySlug = null): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $params = ['status' => 'published'];
        $where = 'WHERE a.status = :status';

        if ($categorySlug !== null) {
            $where .= ' AND c.slug = :category_slug';
            $params['category_slug'] = $categorySlug;
        }

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM articles a LEFT JOIN categories c ON c.id = a.category_id {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "
            SELECT
                a.id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.published_at,
                a.created_at,
                a.updated_at,
                c.name AS category_name,
                c.slug AS category_slug,
                img.url AS image_url,
                img.alt AS image_alt
            FROM articles a
            LEFT JOIN categories c ON c.id = a.category_id
            LEFT JOIN LATERAL (
                SELECT i.url, i.alt
                FROM images i
                WHERE i.article_id = a.id
                ORDER BY i.created_at ASC
                LIMIT 1
            ) img ON true
            {$where}
            ORDER BY COALESCE(a.updated_at, a.created_at) DESC
            LIMIT :per_page OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':per_page', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll() ?: [],
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $sql = '
            SELECT
                a.id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.meta_title,
                a.meta_description,
                a.published_at,
                a.created_at,
                a.updated_at,
                a.category_id,
                c.name AS category_name,
                c.slug AS category_slug
            FROM articles a
            LEFT JOIN categories c ON c.id = a.category_id
            WHERE a.slug = :slug AND a.status = :status
            LIMIT 1
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'slug' => $slug,
            'status' => 'published',
        ]);

        return $stmt->fetch() ?: null;
    }

    public function imagesByArticle(int $articleId): array
    {
        $stmt = $this->pdo->prepare('SELECT url, alt FROM images WHERE article_id = :article_id ORDER BY created_at ASC');
        $stmt->execute(['article_id' => $articleId]);

        return $stmt->fetchAll() ?: [];
    }

    public function relatedByCategory(int $categoryId, int $excludeArticleId, int $limit = 3): array
    {
        $sql = '
            SELECT
                a.id,
                a.title,
                a.slug,
                a.excerpt,
                a.content,
                a.published_at,
                a.created_at,
                a.updated_at,
                img.url AS image_url,
                img.alt AS image_alt
            FROM articles a
            LEFT JOIN LATERAL (
                SELECT i.url, i.alt
                FROM images i
                WHERE i.article_id = a.id
                ORDER BY i.created_at ASC
                LIMIT 1
            ) img ON true
            WHERE a.category_id = :category_id
              AND a.id <> :exclude_id
              AND a.status = :status
            ORDER BY COALESCE(a.updated_at, a.created_at) DESC
            LIMIT :limit
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':exclude_id', $excludeArticleId, PDO::PARAM_INT);
        $stmt->bindValue(':status', 'published', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function findCategoryBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, slug FROM categories WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }
}
