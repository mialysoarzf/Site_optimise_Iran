<!doctype html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Iran Infos') ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'Site d’informations sur la guerre en Iran.') ?>">
    <?php
    $cssPath = __DIR__ . '/../public/assets/style.min.css';
    $inlineCss = @file_get_contents($cssPath);
    ?>
    <?php if ($inlineCss !== false): ?>
        <style><?= $inlineCss ?></style>
    <?php else: ?>
        <link rel="stylesheet" href="/public/assets/style.min.css">
    <?php endif; ?>
</head>
<body class="site-body">
<nav class="site-nav">
    <div class="site-nav-left">
        <a href="/" class="site-brand">IRAN INFOS</a>
        <div class="site-menu">
            <a class="site-menu-link <?= ($currentPath ?? '/') === '/' ? 'is-active' : '' ?>" href="/">DERNIÈRES INFOS</a>
            <a class="site-menu-link <?= ($currentPath ?? '/') === '/articles' ? 'is-active' : '' ?>" href="/articles">ARTICLES</a>
            <a class="site-menu-link <?= ($currentPath ?? '/') === '/a-propos' ? 'is-active' : '' ?>" href="/a-propos">CONTEXTE</a>
        </div>
    </div>
    <div class="site-nav-right">
        <span class="site-badge">Suivi du conflit</span>
    </div>
</nav>

<main class="site-main">
    <div class="site-ticker intelligence-ticker-mask">
        <div class="site-ticker-track animate-marquee">
            <?php if (!empty($tickerItems)): ?>
                <?php foreach ($tickerItems as $ticker): ?>
                    <a class="site-ticker-item" href="/article/<?= e((string) $ticker['slug']) ?>">
                        <span class="site-ticker-dot"></span>
                        <?= e((string) $ticker['title']) ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="site-ticker-item"><span class="site-ticker-dot"></span> Dernières publications en cours de chargement</span>
            <?php endif; ?>
        </div>
    </div>
    <?php require $templatePath; ?>
</main>

<footer class="site-footer">
    <div class="site-footer-main">
        <span class="site-footer-title">IRAN INFOS</span>
        <p class="site-footer-text">© <?= e((string) date('Y')) ?> Iran Infos · veille éditoriale sur le conflit en Iran.</p>
    </div>
    <nav class="site-footer-links">
        <a class="site-footer-link" href="/articles">Articles</a>
        <a class="site-footer-link" href="/a-propos">À propos</a>
        <a class="site-footer-link" href="/">Accueil</a>
    </nav>
</footer>
</body>
</html>
