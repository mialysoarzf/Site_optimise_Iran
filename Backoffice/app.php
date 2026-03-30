<?php

declare(strict_types=1);

function bo_db(array $dbConfig): PDO
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

function bo_csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['_csrf'];
}

function bo_csrf_verify(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['_csrf'])
        && hash_equals((string) $_SESSION['_csrf'], $token);
}

function bo_auth_check(): bool
{
    return !empty($_SESSION['admin_user_id']);
}

function bo_auth_id(): ?int
{
    return isset($_SESSION['admin_user_id']) ? (int) $_SESSION['admin_user_id'] : null;
}

function bo_find_user_by_username(PDO $pdo, string $username): ?array
{
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function bo_auth_attempt(PDO $pdo, string $username, string $password): bool
{
    $user = bo_find_user_by_username($pdo, $username);
    if (!$user) {
        return false;
    }

    if (!password_verify($password, (string) $user['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_user_id'] = (int) $user['id'];
    $_SESSION['admin_username'] = (string) $user['username'];

    return true;
}

function bo_auth_logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}

function bo_dashboard_counters(PDO $pdo): array
{
    $sql = "
        SELECT
            (SELECT COUNT(*) FROM articles) AS total_articles,
            (SELECT COUNT(*) FROM articles WHERE status = 'draft') AS drafts,
            (SELECT COUNT(*) FROM articles WHERE status = 'published') AS published,
            (SELECT COUNT(*) FROM categories) AS categories
    ";

    return $pdo->query($sql)->fetch() ?: [
        'total_articles' => 0,
        'drafts' => 0,
        'published' => 0,
        'categories' => 0,
    ];
}

function bo_dashboard_latest_articles(PDO $pdo, int $limit = 8): array
{
    $stmt = $pdo->prepare(
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

function bo_categories_all(PDO $pdo): array
{
    return $pdo->query('SELECT id, name, slug, created_at FROM categories ORDER BY name ASC')->fetchAll() ?: [];
}

function bo_categories_find(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, name, slug FROM categories WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);

    return $stmt->fetch() ?: null;
}

function bo_categories_slug_exists(PDO $pdo, string $slug, ?int $ignoreId = null): bool
{
    if ($ignoreId) {
        $stmt = $pdo->prepare('SELECT 1 FROM categories WHERE slug = :slug AND id <> :id LIMIT 1');
        $stmt->execute(['slug' => $slug, 'id' => $ignoreId]);
    } else {
        $stmt = $pdo->prepare('SELECT 1 FROM categories WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
    }

    return (bool) $stmt->fetchColumn();
}

function bo_categories_create(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (:name, :slug) RETURNING id');
    $stmt->execute([
        'name' => $data['name'],
        'slug' => $data['slug'],
    ]);

    return (int) $stmt->fetchColumn();
}

function bo_categories_update(PDO $pdo, int $id, array $data): void
{
    $stmt = $pdo->prepare('UPDATE categories SET name = :name, slug = :slug WHERE id = :id');
    $stmt->execute([
        'id' => $id,
        'name' => $data['name'],
        'slug' => $data['slug'],
    ]);
}

function bo_categories_delete(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function bo_categories_article_count(PDO $pdo, int $id): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE category_id = :id');
    $stmt->execute(['id' => $id]);

    return (int) $stmt->fetchColumn();
}

function bo_categories_detach_articles(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('UPDATE articles SET category_id = NULL WHERE category_id = :id');
    $stmt->execute(['id' => $id]);
}

function bo_articles_paginate(PDO $pdo, string $search = '', int $page = 1, int $perPage = 10): array
{
    $offset = max(0, ($page - 1) * $perPage);
    $params = [];
    $where = '';

    if ($search !== '') {
        $where = 'WHERE a.title ILIKE :search';
        $params['search'] = '%' . $search . '%';
    }

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM articles a {$where}");
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

function bo_articles_find(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);

    return $stmt->fetch() ?: null;
}

function bo_articles_slug_exists(PDO $pdo, string $slug, ?int $ignoreId = null): bool
{
    if ($ignoreId) {
        $stmt = $pdo->prepare('SELECT 1 FROM articles WHERE slug = :slug AND id <> :id LIMIT 1');
        $stmt->execute(['slug' => $slug, 'id' => $ignoreId]);
    } else {
        $stmt = $pdo->prepare('SELECT 1 FROM articles WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
    }

    return (bool) $stmt->fetchColumn();
}

function bo_articles_create(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
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

function bo_articles_update(PDO $pdo, int $id, array $data): void
{
    $stmt = $pdo->prepare(
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

function bo_articles_delete(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function bo_images_by_article(PDO $pdo, int $articleId): ?array
{
    $stmt = $pdo->prepare('SELECT id, article_id, url, alt FROM images WHERE article_id = :article_id LIMIT 1');
    $stmt->execute(['article_id' => $articleId]);

    return $stmt->fetch() ?: null;
}

function bo_images_upsert(PDO $pdo, int $articleId, string $url, string $alt): void
{
    $existing = bo_images_by_article($pdo, $articleId);
    if ($existing) {
        $stmt = $pdo->prepare('UPDATE images SET url = :url, alt = :alt WHERE id = :id');
        $stmt->execute([
            'id' => $existing['id'],
            'url' => $url,
            'alt' => $alt,
        ]);
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO images (article_id, url, alt) VALUES (:article_id, :url, :alt)');
    $stmt->execute([
        'article_id' => $articleId,
        'url' => $url,
        'alt' => $alt,
    ]);
}

function bo_images_delete_by_article(PDO $pdo, int $articleId): ?array
{
    $image = bo_images_by_article($pdo, $articleId);
    if (!$image) {
        return null;
    }

    $stmt = $pdo->prepare('DELETE FROM images WHERE article_id = :article_id');
    $stmt->execute(['article_id' => $articleId]);

    return $image;
}

function bo_slugify(string $value): string
{
    $value = mb_strtolower(trim($value));
    $value = preg_replace('/[^\p{L}\p{N}]+/u', '-', $value) ?? '';

    return trim($value, '-');
}

function bo_article_from_old(): array
{
    return [
        'title' => old('title'),
        'slug' => old('slug'),
        'excerpt' => old('excerpt'),
        'content' => old('content'),
        'status' => old('status', 'draft'),
        'category_id' => old('category_id'),
        'meta_title' => old('meta_title'),
        'meta_description' => old('meta_description'),
        'image_alt' => old('image_alt'),
    ];
}

function bo_sanitize_article_input(): array
{
    return [
        'title' => trim((string) ($_POST['title'] ?? '')),
        'slug' => bo_slugify((string) ($_POST['slug'] ?? ($_POST['title'] ?? ''))),
        'excerpt' => trim((string) ($_POST['excerpt'] ?? '')),
        'content' => trim((string) ($_POST['content'] ?? '')),
        'status' => (string) ($_POST['status'] ?? 'draft'),
        'category_id' => ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null,
        'meta_title' => trim((string) ($_POST['meta_title'] ?? '')),
        'meta_description' => trim((string) ($_POST['meta_description'] ?? '')),
        'image_alt' => trim((string) ($_POST['image_alt'] ?? '')),
    ];
}

function bo_validate_article(array $data): array
{
    $errors = [];
    if ($data['title'] === '') {
        $errors[] = 'Le titre est obligatoire.';
    }
    if ($data['content'] === '') {
        $errors[] = 'Le contenu est obligatoire.';
    }
    if ($data['slug'] === '') {
        $errors[] = 'Le slug est obligatoire.';
    }
    if (!in_array($data['status'], ['draft', 'published'], true)) {
        $errors[] = 'Statut invalide.';
    }

    return $errors;
}

function bo_process_upload(array $config, ?array $file): array
{
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['path' => null, 'error' => null];
    }

    if (($file['error'] ?? 0) !== UPLOAD_ERR_OK) {
        return ['path' => null, 'error' => 'Erreur upload image.'];
    }

    if (($file['size'] ?? 0) > $config['app']['max_upload_size']) {
        return ['path' => null, 'error' => 'Image trop lourde (2MB max).'];
    }

    $tmp = (string) $file['tmp_name'];
    $mime = mime_content_type($tmp) ?: '';
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        return ['path' => null, 'error' => 'Type image invalide (jpg/png/webp).'];
    }

    $filename = date('YmdHis') . '-' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
    $targetDir = rtrim((string) $config['app']['upload_dir'], '/\\');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $target = $targetDir . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($tmp, $target)) {
        return ['path' => null, 'error' => 'Impossible de sauvegarder l\'image.'];
    }

    return ['path' => '/uploads/' . $filename, 'error' => null];
}

function bo_show_login(): void
{
    if (bo_auth_check()) {
        redirect('/dashboard');
    }

    view('auth/login', [
        'pageTitle' => 'Connexion admin',
        'metaDescription' => 'Connexion au backoffice',
        'csrf' => bo_csrf_token(),
    ]);
}

function bo_login(PDO $pdo): void
{
    if (!bo_csrf_verify($_POST['_csrf'] ?? null)) {
        set_flash('error', 'Token CSRF invalide.');
        redirect('/login');
    }

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    with_old(['username' => $username]);

    if ($username === '' || $password === '') {
        set_flash('error', 'Identifiants requis.');
        redirect('/login');
    }

    if (!bo_auth_attempt($pdo, $username, $password)) {
        set_flash('error', 'Identifiants invalides.');
        redirect('/login');
    }

    clear_old();
    set_flash('success', 'Connexion réussie.');
    redirect('/dashboard');
}

function bo_logout(): void
{
    if (!bo_csrf_verify($_POST['_csrf'] ?? null)) {
        set_flash('error', 'Token CSRF invalide.');
        redirect('/dashboard');
    }

    bo_auth_logout();
    session_start();
    set_flash('success', 'Déconnexion réussie.');
    redirect('/login');
}

function bo_dashboard(PDO $pdo): void
{
    view('admin/dashboard', [
        'pageTitle' => 'Dashboard',
        'metaDescription' => 'Vue synthèse du backoffice',
        'counters' => bo_dashboard_counters($pdo),
        'latestArticles' => bo_dashboard_latest_articles($pdo),
    ]);
}

function bo_categories_index(PDO $pdo): void
{
    view('categories/index', [
        'pageTitle' => 'Catégories',
        'metaDescription' => 'Gestion des catégories',
        'categories' => bo_categories_all($pdo),
        'csrf' => bo_csrf_token(),
    ]);
}

function bo_categories_create_form(): void
{
    view('categories/form', [
        'pageTitle' => 'Créer une catégorie',
        'metaDescription' => 'Création de catégorie',
        'mode' => 'create',
        'category' => ['name' => old('name'), 'slug' => old('slug')],
        'csrf' => bo_csrf_token(),
    ]);
}

function bo_categories_create_action(PDO $pdo): void
{
    if (!bo_csrf_verify($_POST['_csrf'] ?? null)) {
        set_flash('error', 'Token CSRF invalide.');
        redirect('/categories/create');
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $slug = bo_slugify((string) ($_POST['slug'] ?? $name));

    with_old(['name' => $name, 'slug' => $slug]);

    if ($name === '' || $slug === '') {
        set_flash('error', 'Nom et slug obligatoires.');
        redirect('/categories/create');
    }

    if (bo_categories_slug_exists($pdo, $slug)) {
        set_flash('error', 'Ce slug existe déjà.');
        redirect('/categories/create');
    }

    bo_categories_create($pdo, ['name' => $name, 'slug' => $slug]);
    clear_old();
    set_flash('success', 'Catégorie créée.');
    redirect('/categories');
}

function bo_categories_edit_form(PDO $pdo, int $id): void
{
    $category = bo_categories_find($pdo, $id);
    if (!$category) {
        http_response_code(404);
        view('admin/404', ['pageTitle' => 'Introuvable', 'metaDescription' => 'Ressource introuvable']);
        return;
    }

    view('categories/form', [
        'pageTitle' => 'Modifier une catégorie',
        'metaDescription' => 'Modification de catégorie',
        'mode' => 'edit',
        'category' => [
            'id' => $category['id'],
            'name' => old('name', (string) $category['name']),
            'slug' => old('slug', (string) $category['slug']),
        ],
        'csrf' => bo_csrf_token(),
    ]);
}

function bo_categories_update_action(PDO $pdo, int $id): void
{
    if (!bo_csrf_verify($_POST['_csrf'] ?? null)) {
        set_flash('error', 'Token CSRF invalide.');
        redirect('/categories/edit/' . $id);
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $slug = bo_slugify((string) ($_POST['slug'] ?? $name));

    with_old(['name' => $name, 'slug' => $slug]);

    if ($name === '' || $slug === '') {
        set_flash('error', 'Nom et slug obligatoires.');
        redirect('/categories/edit/' . $id);
    }

    if (bo_categories_slug_exists($pdo, $slug, $id)) {
        set_flash('error', 'Ce slug existe déjà.');
        redirect('/categories/edit/' . $id);
    }

    bo_categories_update($pdo, $id, ['name' => $name, 'slug' => $slug]);
    clear_old();
    set_flash('success', 'Catégorie mise à jour.');
    redirect('/categories');
}

function bo_categories_delete_action(PDO $pdo, int $id): void
{
    if (!bo_csrf_verify($_POST['_csrf'] ?? null)) {
        set_flash('error', 'Token CSRF invalide.');
        redirect('/categories');
    }

    $count = bo_categories_article_count($pdo, $id);
    if ($count > 0) {
        bo_categories_detach_articles($pdo, $id);
        set_flash('success', 'Catégorie supprimée. Les articles associés sont passés sans catégorie.');
    } else {
        set_flash('success', 'Catégorie supprimée.');
    }

    bo_categories_delete($pdo, $id);
    redirect('/categories');
}

function bo_articles_index(PDO $pdo): void
{
    $search = trim((string) ($_GET['q'] ?? ''));
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $pagination = bo_articles_paginate($pdo, $search, $page, 10);

    view('articles/index', [
        'pageTitle' => 'Articles',
        'metaDescription' => 'Gestion des articles',
        'pagination' => $pagination,
        'search' => $search,
        'csrf' => bo_csrf_token(),
    ]);
}

function bo_articles_create_form(PDO $pdo, array $config): void
{
    view('articles/form', [
        'pageTitle' => 'Créer un article',
        'metaDescription' => 'Création d\'article',
        'mode' => 'create',
        'article' => bo_article_from_old(),
        'categories' => bo_categories_all($pdo),
        'image' => null,
        'tinyMceApiKey' => $config['app']['tiny_mce_api_key'] ?? 'no-api-key',
        'csrf' => bo_csrf_token(),
    ]);
}

function bo_articles_create_action(PDO $pdo, array $config, int $authorId): void
{
    if (!bo_csrf_verify($_POST['_csrf'] ?? null)) {
        set_flash('error', 'Token CSRF invalide.');
        redirect('/articles/create');
    }

    $data = bo_sanitize_article_input();
    $errors = bo_validate_article($data);

    if (bo_articles_slug_exists($pdo, $data['slug'])) {
        $errors[] = 'Le slug existe déjà.';
    }

    if ($data['status'] === 'published' && ($data['meta_title'] === '' || $data['meta_description'] === '')) {
        $errors[] = 'Meta title et meta description sont requis pour publier.';
    }

    $imageUpload = bo_process_upload($config, $_FILES['image'] ?? null);
    if ($imageUpload['error']) {
        $errors[] = $imageUpload['error'];
    }

    with_old($data);

    if ($errors) {
        foreach ($errors as $error) {
            set_flash('error', $error);
        }
        redirect('/articles/create');
    }

    $data['author_id'] = $authorId;
    $articleId = bo_articles_create($pdo, $data);

    if ($imageUpload['path']) {
        bo_images_upsert($pdo, $articleId, $imageUpload['path'], $data['image_alt'] ?: $data['title']);
    }

    clear_old();
    set_flash('success', 'Article créé.');
    redirect('/articles');
}

function bo_articles_edit_form(PDO $pdo, array $config, int $id): void
{
    $article = bo_articles_find($pdo, $id);
    if (!$article) {
        http_response_code(404);
        view('admin/404', ['pageTitle' => 'Introuvable', 'metaDescription' => 'Ressource introuvable']);
        return;
    }

    $image = bo_images_by_article($pdo, $id);

    $old = $_SESSION['old'] ?? [];
    $article = [
        'id' => $article['id'],
        'title' => $old['title'] ?? $article['title'],
        'slug' => $old['slug'] ?? $article['slug'],
        'excerpt' => $old['excerpt'] ?? $article['excerpt'],
        'content' => $old['content'] ?? $article['content'],
        'status' => $old['status'] ?? $article['status'],
        'category_id' => $old['category_id'] ?? (string) $article['category_id'],
        'meta_title' => $old['meta_title'] ?? $article['meta_title'],
        'meta_description' => $old['meta_description'] ?? $article['meta_description'],
        'image_alt' => $old['image_alt'] ?? ($image['alt'] ?? ''),
    ];

    view('articles/form', [
        'pageTitle' => 'Modifier un article',
        'metaDescription' => 'Modification d\'article',
        'mode' => 'edit',
        'article' => $article,
        'categories' => bo_categories_all($pdo),
        'image' => $image,
        'tinyMceApiKey' => $config['app']['tiny_mce_api_key'] ?? 'no-api-key',
        'csrf' => bo_csrf_token(),
    ]);
}

function bo_articles_update_action(PDO $pdo, array $config, int $id): void
{
    if (!bo_csrf_verify($_POST['_csrf'] ?? null)) {
        set_flash('error', 'Token CSRF invalide.');
        redirect('/articles/edit/' . $id);
    }

    $article = bo_articles_find($pdo, $id);
    if (!$article) {
        http_response_code(404);
        view('admin/404', ['pageTitle' => 'Introuvable', 'metaDescription' => 'Ressource introuvable']);
        return;
    }

    $data = bo_sanitize_article_input();
    $errors = bo_validate_article($data);

    if (bo_articles_slug_exists($pdo, $data['slug'], $id)) {
        $errors[] = 'Le slug existe déjà.';
    }

    if ($data['status'] === 'published' && ($data['meta_title'] === '' || $data['meta_description'] === '')) {
        $errors[] = 'Meta title et meta description sont requis pour publier.';
    }

    $imageUpload = bo_process_upload($config, $_FILES['image'] ?? null);
    if ($imageUpload['error']) {
        $errors[] = $imageUpload['error'];
    }

    with_old($data);

    if ($errors) {
        foreach ($errors as $error) {
            set_flash('error', $error);
        }
        redirect('/articles/edit/' . $id);
    }

    bo_articles_update($pdo, $id, $data);

    if (!empty($_POST['remove_image'])) {
        $removed = bo_images_delete_by_article($pdo, $id);
        if ($removed) {
            $filePath = __DIR__ . '/' . ltrim((string) $removed['url'], '/');
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
    }

    if ($imageUpload['path']) {
        $existing = bo_images_by_article($pdo, $id);
        if ($existing) {
            $oldFile = __DIR__ . '/' . ltrim((string) $existing['url'], '/');
            if (is_file($oldFile)) {
                unlink($oldFile);
            }
        }
        bo_images_upsert($pdo, $id, $imageUpload['path'], $data['image_alt'] ?: $data['title']);
    } elseif (($data['image_alt'] ?? '') !== '') {
        $existing = bo_images_by_article($pdo, $id);
        if ($existing) {
            bo_images_upsert($pdo, $id, (string) $existing['url'], $data['image_alt']);
        }
    }

    clear_old();
    set_flash('success', 'Article mis à jour.');
    redirect('/articles');
}

function bo_articles_delete_action(PDO $pdo, int $id): void
{
    if (!bo_csrf_verify($_POST['_csrf'] ?? null)) {
        set_flash('error', 'Token CSRF invalide.');
        redirect('/articles');
    }

    $image = bo_images_delete_by_article($pdo, $id);
    bo_articles_delete($pdo, $id);

    if ($image) {
        $filePath = __DIR__ . '/' . ltrim((string) $image['url'], '/');
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    set_flash('success', 'Article supprimé.');
    redirect('/articles');
}

function bo_articles_editor_upload(array $config): void
{
    header('Content-Type: application/json; charset=utf-8');

    $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    $csrfPost = $_POST['_csrf'] ?? null;
    if (!bo_csrf_verify(is_string($csrfHeader) ? $csrfHeader : (is_string($csrfPost) ? $csrfPost : null))) {
        http_response_code(403);
        echo json_encode(['error' => 'CSRF invalide']);
        return;
    }

    $file = $_FILES['file'] ?? null;
    $result = bo_process_upload($config, is_array($file) ? $file : null);

    if ($result['error']) {
        http_response_code(422);
        echo json_encode(['error' => $result['error']]);
        return;
    }

    if (!$result['path']) {
        http_response_code(422);
        echo json_encode(['error' => 'Fichier image manquant']);
        return;
    }

    echo json_encode(['location' => $result['path']]);
}
