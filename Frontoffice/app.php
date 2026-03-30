<?php

declare(strict_types=1);

function fo_db(array $dbConfig): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $dbConfig['host'], $dbConfig['port'], $dbConfig['name']);

    try {
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException) {
        http_response_code(500);
        exit('Erreur interne de connexion à la base de données.');
    }

    return $pdo;
}

function fo_latest_published(PDO $pdo, int $limit = 6): array
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

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':status', 'published', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll() ?: [];
}

function fo_categories_with_published_count(PDO $pdo): array
{
    $sql = '
        SELECT c.id, c.name, c.slug, COUNT(a.id) AS published_count
        FROM categories c
        LEFT JOIN articles a ON a.category_id = c.id AND a.status = :status
        GROUP BY c.id
        HAVING COUNT(a.id) > 0
        ORDER BY c.name ASC
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => 'published']);

    return $stmt->fetchAll() ?: [];
}

function fo_paginate_published(PDO $pdo, int $page = 1, int $perPage = 10, ?string $categorySlug = null): array
{
    $offset = max(0, ($page - 1) * $perPage);
    $params = ['status' => 'published'];
    $where = 'WHERE a.status = :status';

    if ($categorySlug !== null) {
        $where .= ' AND c.slug = :category_slug';
        $params['category_slug'] = $categorySlug;
    }

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM articles a LEFT JOIN categories c ON c.id = a.category_id {$where}");
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

    $stmt = $pdo->prepare($sql);
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

function fo_find_published_by_slug(PDO $pdo, string $slug): ?array
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

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'slug' => $slug,
        'status' => 'published',
    ]);

    return $stmt->fetch() ?: null;
}

function fo_images_by_article(PDO $pdo, int $articleId): array
{
    $stmt = $pdo->prepare('SELECT url, alt FROM images WHERE article_id = :article_id ORDER BY created_at ASC');
    $stmt->execute(['article_id' => $articleId]);

    return $stmt->fetchAll() ?: [];
}

function fo_related_by_category(PDO $pdo, int $categoryId, int $excludeArticleId, int $limit = 3): array
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

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':exclude_id', $excludeArticleId, PDO::PARAM_INT);
    $stmt->bindValue(':status', 'published', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll() ?: [];
}

function fo_find_category_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT id, name, slug FROM categories WHERE slug = :slug LIMIT 1');
    $stmt->execute(['slug' => $slug]);

    return $stmt->fetch() ?: null;
}

function fo_home(PDO $pdo): void
{
    $latest = fo_latest_published($pdo, 7);
    $featured = $latest[0] ?? null;
    $cards = array_slice($latest, 1);

    view('home', [
        'featured' => $featured,
        'latestCards' => $cards,
        'categories' => fo_categories_with_published_count($pdo),
        'pageTitle' => 'Iran Infos - Accueil',
        'metaDescription' => 'Analyses, actualités et dossiers sur la guerre en Iran.',
        'currentPath' => '/',
    ]);
}

function fo_articles(PDO $pdo): void
{
    $page = query_page();
    $pagination = fo_paginate_published($pdo, $page, 10);

    view('articles/index', [
        'pagination' => $pagination,
        'pageTitle' => 'Articles - Iran Infos',
        'metaDescription' => 'Liste des articles publiés sur la guerre en Iran.',
        'currentPath' => '/articles',
        'basePath' => '/articles',
    ]);
}

function fo_article(PDO $pdo, string $slug): void
{
    $article = fo_find_published_by_slug($pdo, $slug);
    if (!$article) {
        fo_not_found();
        return;
    }

    $images = fo_images_by_article($pdo, (int) $article['id']);
    $related = [];
    if (!empty($article['category_id'])) {
        $related = fo_related_by_category($pdo, (int) $article['category_id'], (int) $article['id']);
    }

    $metaTitle = trim((string) ($article['meta_title'] ?? ''));
    view('articles/show', [
        'article' => $article,
        'images' => $images,
        'related' => $related,
        'pageTitle' => $metaTitle !== '' ? $metaTitle : (string) $article['title'],
        'metaDescription' => article_meta_description($article),
        'currentPath' => '/article/' . $slug,
    ]);
}

function fo_category(PDO $pdo, string $slug): void
{
    $category = fo_find_category_by_slug($pdo, $slug);
    if (!$category) {
        fo_not_found();
        return;
    }

    $page = query_page();
    $pagination = fo_paginate_published($pdo, $page, 10, $slug);

    view('categories/show', [
        'category' => $category,
        'pagination' => $pagination,
        'pageTitle' => 'Catégorie ' . $category['name'] . ' - Iran Infos',
        'metaDescription' => 'Articles publiés dans la catégorie ' . $category['name'] . '.',
        'currentPath' => '/categorie/' . $slug,
        'basePath' => '/categorie/' . $slug,
    ]);
}

function fo_about(): void
{
    view('about', [
        'pageTitle' => 'À propos - Iran Infos',
        'metaDescription' => 'Présentation du site et de la ligne éditoriale.',
        'currentPath' => '/a-propos',
    ]);
}

function fo_not_found(): void
{
    http_response_code(404);
    view('404', [
        'pageTitle' => '404 - Page introuvable',
        'metaDescription' => 'La page demandée est introuvable.',
        'currentPath' => '/404',
    ]);
}
