<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Csrf;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ImageRepository;

final class ArticleController
{
    public function __construct(
        private ArticleRepository $articles,
        private CategoryRepository $categories,
        private ImageRepository $images,
        private array $config
    ) {
    }

    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pagination = $this->articles->paginate($search, $page, 10);

        view('articles/index', [
            'pageTitle' => 'Articles',
            'metaDescription' => 'Gestion des articles',
            'pagination' => $pagination,
            'search' => $search,
            'csrf' => Csrf::token(),
        ]);
    }

    public function createForm(): void
    {
        view('articles/form', [
            'pageTitle' => 'Créer un article',
            'metaDescription' => 'Création d\'article',
            'mode' => 'create',
            'article' => $this->articleFromOld(),
            'categories' => $this->categories->all(),
            'image' => null,
            'csrf' => Csrf::token(),
        ]);
    }

    public function create(int $authorId): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('/admin/articles/create');
        }

        $data = $this->sanitizeArticleInput();
        $errors = $this->validateArticle($data);

        if ($this->articles->slugExists($data['slug'])) {
            $errors[] = 'Le slug existe déjà.';
        }

        if ($data['status'] === 'published' && ($data['meta_title'] === '' || $data['meta_description'] === '')) {
            $errors[] = 'Meta title et meta description sont requis pour publier.';
        }

        $imageUpload = $this->processUpload($_FILES['image'] ?? null);
        if ($imageUpload['error']) {
            $errors[] = $imageUpload['error'];
        }

        with_old($data);

        if ($errors) {
            foreach ($errors as $error) {
                set_flash('error', $error);
            }
            redirect('/admin/articles/create');
        }

        $data['author_id'] = $authorId;
        $articleId = $this->articles->create($data);

        if ($imageUpload['path']) {
            $this->images->upsert($articleId, $imageUpload['path'], $data['image_alt'] ?: $data['title']);
        }

        clear_old();
        set_flash('success', 'Article créé.');
        redirect('/admin/articles');
    }

    public function editForm(int $id): void
    {
        $article = $this->articles->find($id);
        if (!$article) {
            http_response_code(404);
            view('admin/404', ['pageTitle' => 'Introuvable', 'metaDescription' => 'Ressource introuvable']);
            return;
        }

        $image = $this->images->byArticle($id);

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
            'categories' => $this->categories->all(),
            'image' => $image,
            'csrf' => Csrf::token(),
        ]);
    }

    public function update(int $id): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('/admin/articles/edit/' . $id);
        }

        $article = $this->articles->find($id);
        if (!$article) {
            http_response_code(404);
            view('admin/404', ['pageTitle' => 'Introuvable', 'metaDescription' => 'Ressource introuvable']);
            return;
        }

        $data = $this->sanitizeArticleInput();
        $errors = $this->validateArticle($data);

        if ($this->articles->slugExists($data['slug'], $id)) {
            $errors[] = 'Le slug existe déjà.';
        }

        if ($data['status'] === 'published' && ($data['meta_title'] === '' || $data['meta_description'] === '')) {
            $errors[] = 'Meta title et meta description sont requis pour publier.';
        }

        $imageUpload = $this->processUpload($_FILES['image'] ?? null);
        if ($imageUpload['error']) {
            $errors[] = $imageUpload['error'];
        }

        with_old($data);

        if ($errors) {
            foreach ($errors as $error) {
                set_flash('error', $error);
            }
            redirect('/admin/articles/edit/' . $id);
        }

        $this->articles->update($id, $data);

        if (!empty($_POST['remove_image'])) {
            $removed = $this->images->deleteByArticle($id);
            if ($removed) {
                $filePath = dirname(__DIR__, 2) . '/' . ltrim($removed['url'], '/');
                if (is_file($filePath)) {
                    unlink($filePath);
                }
            }
        }

        if ($imageUpload['path']) {
            $existing = $this->images->byArticle($id);
            if ($existing) {
                $oldFile = dirname(__DIR__, 2) . '/' . ltrim($existing['url'], '/');
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }
            $this->images->upsert($id, $imageUpload['path'], $data['image_alt'] ?: $data['title']);
        } elseif (($data['image_alt'] ?? '') !== '') {
            $existing = $this->images->byArticle($id);
            if ($existing) {
                $this->images->upsert($id, $existing['url'], $data['image_alt']);
            }
        }

        clear_old();
        set_flash('success', 'Article mis à jour.');
        redirect('/admin/articles');
    }

    public function delete(int $id): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('/admin/articles');
        }

        $image = $this->images->deleteByArticle($id);
        $this->articles->delete($id);

        if ($image) {
            $filePath = dirname(__DIR__, 2) . '/' . ltrim($image['url'], '/');
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }

        set_flash('success', 'Article supprimé.');
        redirect('/admin/articles');
    }

    private function sanitizeArticleInput(): array
    {
        return [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'slug' => self::slugify((string) ($_POST['slug'] ?? ($_POST['title'] ?? ''))),
            'excerpt' => trim((string) ($_POST['excerpt'] ?? '')),
            'content' => trim((string) ($_POST['content'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'draft'),
            'category_id' => ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null,
            'meta_title' => trim((string) ($_POST['meta_title'] ?? '')),
            'meta_description' => trim((string) ($_POST['meta_description'] ?? '')),
            'image_alt' => trim((string) ($_POST['image_alt'] ?? '')),
        ];
    }

    private function validateArticle(array $data): array
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

    private function processUpload(?array $file): array
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['path' => null, 'error' => null];
        }

        if (($file['error'] ?? 0) !== UPLOAD_ERR_OK) {
            return ['path' => null, 'error' => 'Erreur upload image.'];
        }

        if (($file['size'] ?? 0) > $this->config['app']['max_upload_size']) {
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
        $targetDir = rtrim($this->config['app']['upload_dir'], '/\\');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $target = $targetDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($tmp, $target)) {
            return ['path' => null, 'error' => 'Impossible de sauvegarder l\'image.'];
        }

        return ['path' => '/uploads/' . $filename, 'error' => null];
    }

    private function articleFromOld(): array
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

    private static function slugify(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^\p{L}\p{N}]+/u', '-', $value) ?? '';

        return trim($value, '-');
    }
}
