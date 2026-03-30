<section class="page-section">
    <h1 class="page-title">CATÉGORIE: <?= e((string) $category['name']) ?></h1>
    <p class="page-subtitle">Articles publiés dans cette thématique.</p>

    <div class="cards-grid cards-grid-2">
        <?php foreach (($pagination['items'] ?? []) as $item): ?>
            <?php $itemExcerpt = trim((string) ($item['excerpt'] ?? '')) !== '' ? (string) $item['excerpt'] : excerpt_from_content((string) ($item['content'] ?? ''), 165); ?>
            <?php $itemRawImage = trim((string) ($item['image_url'] ?? '')); ?>
            <article class="list-card">
                <?php if ($itemRawImage !== ''): ?>
                    <img
                        src="<?= e(optimized_image_url($itemRawImage, 800, 70)) ?>"
                        srcset="<?= e(optimized_image_srcset($itemRawImage, [320, 480, 640, 800, 960], 70)) ?>"
                        sizes="(max-width: 767px) 100vw, 50vw"
                        alt="<?= e(safe_alt((string) ($item['image_alt'] ?? ''), 'Illustration de ' . (string) $item['title'])) ?>"
                        class="list-card-image"
                        loading="lazy"
                        decoding="async"
                        width="800"
                        height="500"
                    >
                <?php endif; ?>
                <div class="list-card-body">
                    <p class="card-meta"><?= e((string) date('d/m/Y', strtotime((string) ($item['published_at'] ?: $item['updated_at'] ?: $item['created_at'])))) ?></p>
                    <h2 class="card-title card-title-large"><?= e((string) $item['title']) ?></h2>
                    <p class="card-excerpt"><?= e((string) $itemExcerpt) ?></p>
                    <a href="/article/<?= e((string) $item['slug']) ?>" class="card-link">Lire l’article</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (($pagination['pages'] ?? 1) > 1): ?>
        <nav class="pagination" aria-label="Pagination catégorie">
            <?php $current = (int) ($pagination['page'] ?? 1); ?>
            <?php $pages = (int) ($pagination['pages'] ?? 1); ?>

            <?php if ($current > 1): ?>
                <a href="<?= e(url_with_page($basePath ?? ('/categorie/' . $category['slug']), $current - 1)) ?>" class="pagination-link">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="<?= e(url_with_page($basePath ?? ('/categorie/' . $category['slug']), $i)) ?>" class="pagination-link <?= $i === $current ? 'is-current' : '' ?>"><?= e((string) $i) ?></a>
            <?php endfor; ?>

            <?php if ($current < $pages): ?>
                <a href="<?= e(url_with_page($basePath ?? ('/categorie/' . $category['slug']), $current + 1)) ?>" class="pagination-link">Suivant</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</section>
