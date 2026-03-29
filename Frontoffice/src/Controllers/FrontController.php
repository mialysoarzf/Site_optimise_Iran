<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\FrontRepository;

final class FrontController
{
    public function __construct(private FrontRepository $repository)
    {
    }

    public function home(): void
    {
        $latest = $this->repository->latestPublished(7);
        $featured = $latest[0] ?? null;
        $cards = array_slice($latest, 1);

        view('home', [
            'featured' => $featured,
            'latestCards' => $cards,
            'categories' => $this->repository->categoriesWithPublishedCount(),
            'pageTitle' => 'Iran Infos - Accueil',
            'metaDescription' => 'Analyses, actualités et dossiers sur la guerre en Iran.',
            'currentPath' => '/',
        ]);
    }

    public function articles(): void
    {
        $page = query_page();
        $pagination = $this->repository->paginatePublished($page, 10);

        view('articles/index', [
            'pagination' => $pagination,
            'pageTitle' => 'Articles - Iran Infos',
            'metaDescription' => 'Liste des articles publiés sur la guerre en Iran.',
            'currentPath' => '/articles',
            'basePath' => '/articles',
        ]);
    }

    public function article(string $slug): void
    {
        $article = $this->repository->findPublishedBySlug($slug);
        if (!$article) {
            $this->notFound();
            return;
        }

        $images = $this->repository->imagesByArticle((int) $article['id']);
        $related = [];
        if (!empty($article['category_id'])) {
            $related = $this->repository->relatedByCategory((int) $article['category_id'], (int) $article['id']);
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

    public function category(string $slug): void
    {
        $category = $this->repository->findCategoryBySlug($slug);
        if (!$category) {
            $this->notFound();
            return;
        }

        $page = query_page();
        $pagination = $this->repository->paginatePublished($page, 10, $slug);

        view('categories/show', [
            'category' => $category,
            'pagination' => $pagination,
            'pageTitle' => 'Catégorie ' . $category['name'] . ' - Iran Infos',
            'metaDescription' => 'Articles publiés dans la catégorie ' . $category['name'] . '.',
            'currentPath' => '/categorie/' . $slug,
            'basePath' => '/categorie/' . $slug,
        ]);
    }

    public function about(): void
    {
        view('about', [
            'pageTitle' => 'À propos - Iran Infos',
            'metaDescription' => 'Présentation du site et de la ligne éditoriale.',
            'currentPath' => '/a-propos',
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        view('404', [
            'pageTitle' => '404 - Page introuvable',
            'metaDescription' => 'La page demandée est introuvable.',
            'currentPath' => '/404',
        ]);
    }
}
