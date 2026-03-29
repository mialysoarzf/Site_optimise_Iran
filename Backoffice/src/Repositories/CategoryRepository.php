<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class CategoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT id, name, slug, created_at FROM categories ORDER BY name ASC')->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, slug FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        if ($ignoreId) {
            $stmt = $this->pdo->prepare('SELECT 1 FROM categories WHERE slug = :slug AND id <> :id LIMIT 1');
            $stmt->execute(['slug' => $slug, 'id' => $ignoreId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT 1 FROM categories WHERE slug = :slug LIMIT 1');
            $stmt->execute(['slug' => $slug]);
        }

        return (bool) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO categories (name, slug) VALUES (:name, :slug) RETURNING id');
        $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE categories SET name = :name, slug = :slug WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function articleCount(int $id): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM articles WHERE category_id = :id');
        $stmt->execute(['id' => $id]);

        return (int) $stmt->fetchColumn();
    }

    public function detachArticles(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE articles SET category_id = NULL WHERE category_id = :id');
        $stmt->execute(['id' => $id]);
    }
}
