<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Csrf;
use App\Repositories\CategoryRepository;

final class CategoryController
{
    public function __construct(private CategoryRepository $categories)
    {
    }

    public function index(): void
    {
        view('categories/index', [
            'pageTitle' => 'Catégories',
            'metaDescription' => 'Gestion des catégories',
            'categories' => $this->categories->all(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function createForm(): void
    {
        view('categories/form', [
            'pageTitle' => 'Créer une catégorie',
            'metaDescription' => 'Création de catégorie',
            'mode' => 'create',
            'category' => ['name' => old('name'), 'slug' => old('slug')],
            'csrf' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('/admin/categories/create');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = self::slugify((string) ($_POST['slug'] ?? $name));

        with_old(['name' => $name, 'slug' => $slug]);

        if ($name === '' || $slug === '') {
            set_flash('error', 'Nom et slug obligatoires.');
            redirect('/admin/categories/create');
        }

        if ($this->categories->slugExists($slug)) {
            set_flash('error', 'Ce slug existe déjà.');
            redirect('/admin/categories/create');
        }

        $this->categories->create(['name' => $name, 'slug' => $slug]);
        clear_old();
        set_flash('success', 'Catégorie créée.');
        redirect('/admin/categories');
    }

    public function editForm(int $id): void
    {
        $category = $this->categories->find($id);
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
            'csrf' => Csrf::token(),
        ]);
    }

    public function update(int $id): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('/admin/categories/edit/' . $id);
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = self::slugify((string) ($_POST['slug'] ?? $name));

        with_old(['name' => $name, 'slug' => $slug]);

        if ($name === '' || $slug === '') {
            set_flash('error', 'Nom et slug obligatoires.');
            redirect('/admin/categories/edit/' . $id);
        }

        if ($this->categories->slugExists($slug, $id)) {
            set_flash('error', 'Ce slug existe déjà.');
            redirect('/admin/categories/edit/' . $id);
        }

        $this->categories->update($id, ['name' => $name, 'slug' => $slug]);
        clear_old();
        set_flash('success', 'Catégorie mise à jour.');
        redirect('/admin/categories');
    }

    public function delete(int $id): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('/admin/categories');
        }

        $count = $this->categories->articleCount($id);
        if ($count > 0) {
            $this->categories->detachArticles($id);
            set_flash('success', 'Catégorie supprimée. Les articles associés sont passés sans catégorie.');
        } else {
            set_flash('success', 'Catégorie supprimée.');
        }

        $this->categories->delete($id);
        redirect('/admin/categories');
    }

    private static function slugify(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^\p{L}\p{N}]+/u', '-', $value) ?? '';

        return trim($value, '-');
    }
}
