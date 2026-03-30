<?php
$hero = $featured;
$heroTitle = $hero['title'] ?? 'Veille stratégique sur la guerre en Iran';
$heroExcerpt = $hero['excerpt'] ?? excerpt_from_content((string) ($hero['content'] ?? ''), 220);
$heroRawImage = trim((string) ($hero['image_url'] ?? ''));
$heroImage = media_url($heroRawImage) ?: 'https://images.unsplash.com/photo-1541417904950-b855846fe074?auto=format&fit=crop&w=1600&q=80';
$heroImageOptimized = $heroRawImage !== '' ? optimized_image_url($heroRawImage, 1280, 72) : $heroImage;
$heroImageSrcset = $heroRawImage !== '' ? optimized_image_srcset($heroRawImage, [480, 768, 1024, 1280, 1600], 72) : '';
$heroAlt = safe_alt($hero['image_alt'] ?? '', 'Image de couverture du dossier principal');
$heroImagePath = (string) parse_url((string) $heroImage, PHP_URL_PATH);
$heroImageExtension = strtolower(pathinfo($heroImagePath, PATHINFO_EXTENSION));
$heroImageType = $heroImageExtension !== '' ? strtoupper($heroImageExtension) : 'URL CDN';
$heroImageSource = (string) parse_url((string) $heroImage, PHP_URL_HOST);
?>

<section class="home-hero">
    <div class="home-hero-media">
        <img
            src="<?= e((string) $heroImageOptimized) ?>"
            <?php if ($heroImageSrcset !== ''): ?>srcset="<?= e($heroImageSrcset) ?>" sizes="100vw"<?php endif; ?>
            alt="<?= e((string) $heroAlt) ?>"
            class="home-hero-image"
            fetchpriority="high"
            decoding="async"
            loading="eager"
            width="1280"
            height="720"
        >
        <div class="home-hero-overlay"></div>
    </div>
    <div class="home-hero-content-wrap">
        <div class="home-hero-content">
            <span class="home-chip">Dossier principal</span>
            <h1 class="home-hero-title"><?= e((string) $heroTitle) ?></h1>
            <p class="home-hero-excerpt"><?= e((string) $heroExcerpt) ?></p>
            <?php if (!empty($hero['slug'])): ?>
                <a href="/article/<?= e((string) $hero['slug']) ?>" class="home-hero-cta">Lire l’article</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="home-section home-latest">
    <div class="home-section-head">
        <div>
            <h2 class="home-section-title">DERNIERS ARTICLES</h2>
            <p class="home-section-subtitle">Sélection rapide des publications clés sur la situation en Iran.</p>
        </div>
        <a href="/articles" class="home-section-link">Voir tout</a>
    </div>
    <div class="home-cards-grid home-cards-grid-3">
        <?php foreach ($latestCards as $item): ?>
            <?php $cardExcerpt = trim((string) ($item['excerpt'] ?? '')) !== '' ? (string) $item['excerpt'] : excerpt_from_content((string) ($item['content'] ?? ''), 150); ?>
            <?php $cardRawImage = trim((string) ($item['image_url'] ?? '')); ?>
            <article class="home-card">
                <div class="home-card-media">
                    <a href="/article/<?= e((string) $item['slug']) ?>" class="home-card-media-link" aria-label="Ouvrir l’article <?= e((string) $item['title']) ?>">
                        <img
                            src="<?= e($cardRawImage !== '' ? optimized_image_url($cardRawImage, 640, 70) : ('https://images.unsplash.com/photo-1518544866330-95a9f2f8fcd0?auto=format&fit=crop&w=900&q=75')) ?>"
                            <?php if ($cardRawImage !== ''): ?>srcset="<?= e(optimized_image_srcset($cardRawImage, [320, 480, 640, 800], 70)) ?>" sizes="(max-width: 767px) 100vw, (max-width: 1200px) 33vw, 360px"<?php endif; ?>
                            alt="<?= e(safe_alt((string) ($item['image_alt'] ?? ''), 'Illustration de ' . ((string) ($item['title'] ?? 'article')))) ?>"
                            class="home-card-image"
                            loading="lazy"
                            decoding="async"
                            width="640"
                            height="420"
                        >
                    </a>
                    <?php if (!empty($item['category_name'])): ?>
                        <div class="home-card-category"><?= e((string) $item['category_name']) ?></div>
                    <?php endif; ?>
                </div>
                <h3 class="home-card-title"><?= e((string) $item['title']) ?></h3>
                <p class="home-card-excerpt"><?= e((string) $cardExcerpt) ?></p>
                <p class="home-card-meta"><?= e((string) date('d/m/Y', strtotime((string) ($item['published_at'] ?: $item['updated_at'] ?: $item['created_at'])))) ?></p>
                <a href="/article/<?= e((string) $item['slug']) ?>" class="home-card-link">Lire l’article</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="home-section home-visual">
    <div class="home-visual-grid">
        <div>
            <h2 class="home-section-title home-section-title-small">POINT VISUEL</h2>
            <p class="home-card-excerpt home-visual-desc">Informations sur l’image principale utilisée pour illustrer le contexte du conflit.</p>
            <dl class="home-visual-list">
                <div>
                    <dt class="home-visual-term">Type d’image</dt>
                    <dd class="home-visual-value"><?= e((string) $heroImageType) ?></dd>
                </div>
                <div>
                    <dt class="home-visual-term">Description (alt)</dt>
                    <dd class="home-visual-value"><?= e((string) $heroAlt) ?></dd>
                </div>
                <div>
                    <dt class="home-visual-term">Source</dt>
                    <dd class="home-visual-value"><?= e((string) ($heroImageSource !== '' ? $heroImageSource : 'source interne')) ?></dd>
                </div>
            </dl>
        </div>
        <div class="home-visual-image-wrap">
            <img
                src="<?= e((string) ($heroRawImage !== '' ? optimized_image_url($heroRawImage, 960, 72) : $heroImage)) ?>"
                <?php if ($heroRawImage !== ''): ?>srcset="<?= e(optimized_image_srcset($heroRawImage, [480, 768, 960, 1280], 72)) ?>" sizes="(max-width: 767px) 100vw, 50vw"<?php endif; ?>
                alt="<?= e((string) $heroAlt) ?>"
                class="home-visual-image"
                loading="lazy"
                decoding="async"
                width="960"
                height="540"
            >
        </div>
    </div>
</section>

<section class="home-categories">
    <div class="home-categories-grid">
        <div>
            <h2 class="home-categories-title">CATEGORIES PRINCIPALES</h2>
            <p class="home-card-excerpt">Filtrer les publications par thématique pour suivre l’évolution du conflit et des impacts régionaux.</p>
        </div>
        <div class="home-categories-list">
            <?php foreach ($categories as $category): ?>
                <a href="/categorie/<?= e((string) $category['slug']) ?>" class="home-category-item">
                    <h3 class="home-category-title"><?= e((string) $category['name']) ?></h3>
                    <p class="home-category-meta"><?= e((string) $category['published_count']) ?> articles publiés</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
