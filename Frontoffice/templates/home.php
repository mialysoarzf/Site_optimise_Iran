<?php
$hero = $featured;
$heroTitle = $hero['title'] ?? 'Veille stratégique sur la guerre en Iran';
$heroExcerpt = $hero['excerpt'] ?? excerpt_from_content((string) ($hero['content'] ?? ''), 220);
$heroImage = media_url($hero['image_url'] ?? '') ?: 'https://images.unsplash.com/photo-1541417904950-b855846fe074?auto=format&fit=crop&w=1600&q=80';
$heroAlt = safe_alt($hero['image_alt'] ?? '', 'Image de couverture du dossier principal');
$heroImagePath = (string) parse_url((string) $heroImage, PHP_URL_PATH);
$heroImageExtension = strtolower(pathinfo($heroImagePath, PATHINFO_EXTENSION));
$heroImageType = $heroImageExtension !== '' ? strtoupper($heroImageExtension) : 'URL CDN';
$heroImageSource = (string) parse_url((string) $heroImage, PHP_URL_HOST);
?>

<section class="relative h-[70vh] min-h-[500px] w-full overflow-hidden bg-primary-container">
    <div class="absolute inset-0">
        <img src="<?= e((string) $heroImage) ?>" alt="<?= e((string) $heroAlt) ?>" class="w-full h-full object-cover opacity-60 grayscale-[0.2]" fetchpriority="high">
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-transparent to-transparent"></div>
    </div>
    <div class="absolute inset-0 flex flex-col justify-end p-6 md:p-16">
        <div class="max-w-4xl space-y-5">
            <span class="inline-flex items-center gap-2 bg-on-tertiary-container px-3 py-1 text-white font-label text-[10px] font-bold tracking-[0.3em] uppercase">Dossier principal</span>
            <h1 class="font-headline text-4xl md:text-6xl text-white font-black leading-none tracking-tighter"><?= e((string) $heroTitle) ?></h1>
            <p class="font-body text-lg text-white/85 max-w-3xl leading-relaxed"><?= e((string) $heroExcerpt) ?></p>
            <?php if (!empty($hero['slug'])): ?>
                <a href="/article/<?= e((string) $hero['slug']) ?>" class="inline-block bg-white text-primary px-8 py-3 text-[11px] font-label font-black tracking-widest uppercase hover:bg-surface-container-high">Lire l’article</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="px-6 md:px-12 py-16 max-w-7xl mx-auto">
    <div class="flex items-baseline justify-between mb-10 border-b border-outline-variant pb-4">
        <div>
            <h2 class="font-headline text-3xl font-bold tracking-tight">DERNIERS ARTICLES</h2>
            <p class="font-label text-[10px] tracking-[0.2em] uppercase opacity-60 mt-2">Sélection rapide des publications clés sur la situation en Iran.</p>
        </div>
        <a href="/articles" class="font-label text-[10px] tracking-[0.2em] uppercase opacity-60">Voir tout</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <?php foreach ($latestCards as $item): ?>
            <?php $cardExcerpt = trim((string) ($item['excerpt'] ?? '')) !== '' ? (string) $item['excerpt'] : excerpt_from_content((string) ($item['content'] ?? ''), 150); ?>
            <article class="flex flex-col group">
                <div class="mb-5 relative h-48 bg-surface-container overflow-hidden">
                    <img
                        src="<?= e(media_url((string) ($item['image_url'] ?? '')) ?: 'https://images.unsplash.com/photo-1518544866330-95a9f2f8fcd0?auto=format&fit=crop&w=900&q=75') ?>"
                        alt="<?= e(safe_alt((string) ($item['image_alt'] ?? ''), 'Illustration de ' . ((string) ($item['title'] ?? 'article')))) ?>"
                        class="w-full h-full object-cover grayscale brightness-90 group-hover:scale-105 transition-transform duration-700"
                        loading="lazy"
                    >
                    <?php if (!empty($item['category_name'])): ?>
                        <div class="absolute top-3 left-3 bg-primary text-white text-[9px] font-bold tracking-widest px-2 py-0.5 uppercase"><?= e((string) $item['category_name']) ?></div>
                    <?php endif; ?>
                </div>
                <h3 class="font-headline text-xl font-bold mb-2 group-hover:text-on-tertiary-container transition-colors"><?= e((string) $item['title']) ?></h3>
                <p class="font-body text-sm text-on-surface-variant leading-relaxed mb-3"><?= e((string) $cardExcerpt) ?></p>
                <p class="font-label text-[10px] tracking-[0.18em] uppercase opacity-60 mb-4"><?= e((string) date('d/m/Y', strtotime((string) ($item['published_at'] ?: $item['updated_at'] ?: $item['created_at'])))) ?></p>
                <a href="/article/<?= e((string) $item['slug']) ?>" class="mt-auto flex items-center gap-2 font-label text-[10px] font-bold tracking-[0.2em] uppercase text-primary border-b border-primary w-fit pb-1">Lire l’article</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="px-6 md:px-12 pb-16 max-w-7xl mx-auto">
    <div class="border border-outline-variant bg-surface-container-lowest p-6 md:p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="font-headline text-3xl font-bold tracking-tight mb-3">POINT VISUEL</h2>
            <p class="font-body text-sm text-on-surface-variant leading-relaxed mb-6">Informations sur l’image principale utilisée pour illustrer le contexte du conflit.</p>
            <dl class="space-y-3">
                <div>
                    <dt class="font-label text-[10px] tracking-[0.18em] uppercase opacity-60">Type d’image</dt>
                    <dd class="font-body text-base text-on-surface"><?= e((string) $heroImageType) ?></dd>
                </div>
                <div>
                    <dt class="font-label text-[10px] tracking-[0.18em] uppercase opacity-60">Description (alt)</dt>
                    <dd class="font-body text-base text-on-surface"><?= e((string) $heroAlt) ?></dd>
                </div>
                <div>
                    <dt class="font-label text-[10px] tracking-[0.18em] uppercase opacity-60">Source</dt>
                    <dd class="font-body text-base text-on-surface"><?= e((string) ($heroImageSource !== '' ? $heroImageSource : 'source interne')) ?></dd>
                </div>
            </dl>
        </div>
        <div class="bg-surface-container overflow-hidden border border-outline-variant">
            <img src="<?= e((string) $heroImage) ?>" alt="<?= e((string) $heroAlt) ?>" class="w-full h-full max-h-[360px] object-cover" loading="lazy">
        </div>
    </div>
</section>

<section class="bg-surface-container-low py-20 px-6 md:px-12">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-10">
        <div>
            <h2 class="font-headline text-4xl font-black mb-4 leading-tight">CATEGORIES PRINCIPALES</h2>
            <p class="font-body text-sm text-on-surface-variant leading-loose">Filtrer les publications par thématique pour suivre l’évolution du conflit et des impacts régionaux.</p>
        </div>
        <div class="space-y-4">
            <?php foreach ($categories as $category): ?>
                <a href="/categorie/<?= e((string) $category['slug']) ?>" class="block border border-outline p-4 hover:bg-surface-container transition-colors">
                    <h3 class="font-headline text-2xl font-bold"><?= e((string) $category['name']) ?></h3>
                    <p class="font-label text-[10px] tracking-[0.18em] uppercase opacity-60 mt-1"><?= e((string) $category['published_count']) ?> articles publiés</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
