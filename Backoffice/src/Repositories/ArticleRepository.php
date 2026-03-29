<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ArticleRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function paginate(string $search = '', int $page = 1, int $perPage = 10): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = 'WHERE a.title ILIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM articles a {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "
            SELECT a.id, a.title, a.slug, a.status, a.updated_at, c.name AS category_name
            FROM articles a
            LEFT JOIN categories c ON c.id = a.category_id
            {$where}
            ORDER BY a.updated_at DESC
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

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM articles WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        if ($ignoreId) {
            $stmt = $this->pdo->prepare('SELECT 1 FROM articles WHERE slug = :slug AND id <> :id LIMIT 1');
            $stmt->execute(['slug' => $slug, 'id' => $ignoreId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT 1 FROM articles WHERE slug = :slug LIMIT 1');
            $stmt->execute(['slug' => $slug]);
        }

        return (bool) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO articles (title, slug, excerpt, content, status, category_id, meta_title, meta_description, author_id)
             VALUES (:title, :slug, :excerpt, :content, :status, :category_id, :meta_title, :meta_description, :author_id)
             RETURNING id'
        );

        $stmt->execute([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?: null,
            'content' => $data['content'],
            'status' => $data['status'],
            'category_id' => $data['category_id'] ?: null,
            'meta_title' => $data['meta_title'] ?: null,
            'meta_description' => $data['meta_description'] ?: null,
            'author_id' => $data['author_id'] ?: null,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE articles
             SET title = :title,
                 slug = :slug,
                 excerpt = :excerpt,
                 content = :content,
                 status = :status,
                 category_id = :category_id,
                 meta_title = :meta_title,
                 meta_description = :meta_description,
                 updated_at = NOW()
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?: null,
            'content' => $data['content'],
            'status' => $data['status'],
            'category_id' => $data['category_id'] ?: null,
            'meta_title' => $data['meta_title'] ?: null,
            'meta_description' => $data['meta_description'] ?: null,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
