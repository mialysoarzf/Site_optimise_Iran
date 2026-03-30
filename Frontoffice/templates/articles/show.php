<article class="bg-surface py-16 px-6 md:px-12 border-b border-outline-variant">
    <div class="max-w-[900px] mx-auto">
        <?php
        $rawContent = (string) ($article['content'] ?? '');
        $decodedContent = html_entity_decode($rawContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $safeContent = strip_tags($decodedContent, '<p><br><ul><ol><li><strong><b><em><i><h2><h3><h4><blockquote><a>');
        ?>
        <p class="font-label text-[10px] tracking-[0.18em] uppercase opacity-60 mb-3">
            <?= e((string) date('d/m/Y', strtotime((string) ($article['published_at'] ?: $article['updated_at'] ?: $article['created_at'])))) ?>
            <?php if (!empty($article['category_name'])): ?> · <a href="/categorie/<?= e((string) $article['category_slug']) ?>"><?= e((string) $article['category_name']) ?></a><?php endif; ?>
        </p>
        <h1 class="font-headline text-5xl md:text-6xl font-black tracking-tight mb-8"><?= e((string) $article['title']) ?></h1>

        <?php if (!empty($images)): ?>
            <section class="mb-10 space-y-5" aria-label="Images de l'article">
                <?php foreach ($images as $image): ?>
                    <?php $imgAlt = safe_alt((string) ($image['alt'] ?? ''), 'Illustration de l\'article'); ?>
                    <img src="<?= e(media_url((string) $image['url'])) ?>" alt="<?= e($imgAlt) ?>" class="w-full h-auto object-cover border border-outline-variant" loading="lazy">
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <section class="prose-article">
            <?= $safeContent ?>
        </section>
    </div>
</article>

<?php if (!empty($related)): ?>
    <section class="px-6 md:px-12 py-16 max-w-7xl mx-auto">
        <h2 class="font-headline text-3xl font-bold mb-8">ARTICLES LIÉS</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($related as $item): ?>
                <?php $relatedExcerpt = trim((string) ($item['excerpt'] ?? '')) !== '' ? (string) $item['excerpt'] : excerpt_from_content((string) ($item['content'] ?? ''), 120); ?>
                <article class="border border-outline-variant p-5 bg-surface-container-lowest">
                    <?php if (!empty($item['image_url'])): ?>
                        <img src="<?= e(media_url((string) $item['image_url'])) ?>" alt="<?= e(safe_alt((string) ($item['image_alt'] ?? ''), 'Illustration de ' . (string) $item['title'])) ?>" class="w-full h-40 object-cover mb-4" loading="lazy">
                    <?php endif; ?>
                    <h3 class="font-headline text-2xl font-bold mb-2"><?= e((string) $item['title']) ?></h3>
                    <p class="font-body text-sm text-on-surface-variant mb-3"><?= e((string) $relatedExcerpt) ?></p>
                    <a href="/article/<?= e((string) $item['slug']) ?>" class="font-label text-[10px] font-bold tracking-[0.2em] uppercase border-b border-primary pb-1">Lire</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
