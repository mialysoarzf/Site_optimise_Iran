<section class="px-6 md:px-12 py-16 max-w-7xl mx-auto">
    <h1 class="font-headline text-5xl font-black tracking-tight mb-4">ARTICLES</h1>
    <p class="font-body text-on-surface-variant mb-10">Tous les contenus publiés, triés du plus récent au plus ancien.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php foreach (($pagination['items'] ?? []) as $item): ?>
            <?php $itemExcerpt = trim((string) ($item['excerpt'] ?? '')) !== '' ? (string) $item['excerpt'] : excerpt_from_content((string) ($item['content'] ?? ''), 165); ?>
            <article class="border border-outline-variant bg-surface-container-lowest overflow-hidden">
                <?php if (!empty($item['image_url'])): ?>
                    <img src="<?= e(media_url((string) $item['image_url'])) ?>" alt="<?= e(safe_alt((string) ($item['image_alt'] ?? ''), 'Illustration de ' . (string) $item['title'])) ?>" class="w-full h-56 object-cover" loading="lazy">
                <?php endif; ?>
                <div class="p-6">
                    <p class="font-label text-[10px] tracking-[0.18em] uppercase opacity-60 mb-2">
                        <?= e((string) date('d/m/Y', strtotime((string) ($item['published_at'] ?: $item['updated_at'] ?: $item['created_at'])))) ?>
                        <?php if (!empty($item['category_name'])): ?> · <?= e((string) $item['category_name']) ?><?php endif; ?>
                    </p>
                    <h2 class="font-headline text-3xl font-bold mb-3"><?= e((string) $item['title']) ?></h2>    
                    <p class="font-body text-on-surface-variant mb-4 leading-relaxed"><?= e((string) $itemExcerpt) ?></p>
                    <a href="/article/<?= e((string) $item['slug']) ?>" class="font-label text-[10px] font-bold tracking-[0.2em] uppercase border-b border-primary pb-1">Lire l’article</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (($pagination['pages'] ?? 1) > 1): ?>
        <nav class="mt-10 flex items-center gap-2" aria-label="Pagination des articles">
            <?php $current = (int) ($pagination['page'] ?? 1); ?>
            <?php $pages = (int) ($pagination['pages'] ?? 1); ?>

            <?php if ($current > 1): ?>
                <a href="<?= e(url_with_page($basePath ?? '/articles', $current - 1)) ?>" class="px-4 py-2 border border-outline text-sm">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="<?= e(url_with_page($basePath ?? '/articles', $i)) ?>" class="px-4 py-2 border text-sm <?= $i === $current ? 'bg-primary text-on-primary border-primary' : 'border-outline' ?>"><?= e((string) $i) ?></a>
            <?php endfor; ?>

            <?php if ($current < $pages): ?>
                <a href="<?= e(url_with_page($basePath ?? '/articles', $current + 1)) ?>" class="px-4 py-2 border border-outline text-sm">Suivant</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</section>
