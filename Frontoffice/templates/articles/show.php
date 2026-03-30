<article class="article-page">
    <div class="article-page-inner">
        <?php
        $rawContent = (string) ($article['content'] ?? '');
        $decodedContent = html_entity_decode($rawContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $safeContent = strip_tags($decodedContent, '<p><br><ul><ol><li><strong><b><em><i><h2><h3><h4><blockquote><a>');
        ?>
        <p class="card-meta">
            <?= e((string) date('d/m/Y', strtotime((string) ($article['published_at'] ?: $article['updated_at'] ?: $article['created_at'])))) ?>
            <?php if (!empty($article['category_name'])): ?> · <a href="/categorie/<?= e((string) $article['category_slug']) ?>"><?= e((string) $article['category_name']) ?></a><?php endif; ?>
        </p>
        <h1 class="article-title"><?= e((string) $article['title']) ?></h1>

        <?php if (!empty($images)): ?>
            <section class="article-images" aria-label="Images de l'article">
                <?php foreach ($images as $image): ?>
                    <?php $imgAlt = safe_alt((string) ($image['alt'] ?? ''), 'Illustration de l\'article'); ?>
                    <?php $rawImage = trim((string) ($image['url'] ?? '')); ?>
                    <?php if ($rawImage !== ''): ?>
                        <a href="<?= e(optimized_image_url($rawImage, 1800, 78)) ?>" class="article-image-link" target="_blank" rel="noopener noreferrer" aria-label="Ouvrir l’image en grand format">
                            <img
                                src="<?= e(optimized_image_url($rawImage, 1200, 72)) ?>"
                                srcset="<?= e(optimized_image_srcset($rawImage, [480, 768, 1024, 1200, 1600], 72)) ?>"
                                sizes="(max-width: 960px) 100vw, 900px"
                                alt="<?= e($imgAlt) ?>"
                                class="article-image"
                                loading="lazy"
                                decoding="async"
                                width="1200"
                                height="760"
                            >
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <section class="prose-article">
            <?= $safeContent ?>
        </section>
    </div>
</article>

<?php if (!empty($related)): ?>
    <section class="page-section">
        <h2 class="section-title">ARTICLES LIÉS</h2>
        <div class="cards-grid cards-grid-3">
            <?php foreach ($related as $item): ?>
                <?php $relatedExcerpt = trim((string) ($item['excerpt'] ?? '')) !== '' ? (string) $item['excerpt'] : excerpt_from_content((string) ($item['content'] ?? ''), 120); ?>
                <?php $relatedRawImage = trim((string) ($item['image_url'] ?? '')); ?>
                <article class="related-card">
                    <?php if ($relatedRawImage !== ''): ?>
                        <a href="/article/<?= e((string) $item['slug']) ?>" class="related-card-media-link" aria-label="Ouvrir l’article lié <?= e((string) $item['title']) ?>">
                            <img
                                src="<?= e(optimized_image_url($relatedRawImage, 640, 70)) ?>"
                                srcset="<?= e(optimized_image_srcset($relatedRawImage, [320, 480, 640, 800], 70)) ?>"
                                sizes="(max-width: 767px) 100vw, (max-width: 1200px) 33vw, 360px"
                                alt="<?= e(safe_alt((string) ($item['image_alt'] ?? ''), 'Illustration de ' . (string) $item['title'])) ?>"
                                class="related-card-image"
                                loading="lazy"
                                decoding="async"
                                width="640"
                                height="400"
                            >
                        </a>
                    <?php endif; ?>
                    <div class="related-card-content">
                        <h3 class="card-title"><?= e((string) $item['title']) ?></h3>
                        <p class="card-excerpt"><?= e((string) $relatedExcerpt) ?></p>
                        <a href="/article/<?= e((string) $item['slug']) ?>" class="card-link">Lire</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
