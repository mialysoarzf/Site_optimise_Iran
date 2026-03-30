<?php
$flash = get_flash();
$username = $_SESSION['admin_username'] ?? null;
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Backoffice') ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'Backoffice') ?>">
    <link rel="stylesheet" href="/public/assets/style.min.css">
</head>
<body>
<header class="topbar">
    <div class="container topbar-row">
        <a class="logo" href="/dashboard">Iran Infos · Admin</a>
        <?php if ($username): ?>
            <nav class="nav">
                <a href="/dashboard">Dashboard</a>
                <a href="/articles">Articles</a>
                <a href="/categories">Catégories</a>
                <form method="post" action="/logout" class="inline-form">
                    <input type="hidden" name="_csrf" value="<?= e(bo_csrf_token()) ?>">
                    <button type="submit" class="btn btn-link">Déconnexion (<?= e((string) $username) ?>)</button>
                </form>
            </nav>
        <?php endif; ?>
    </div>
</header>

<main class="container main-content">
    <?php require __DIR__ . '/partials/flash.php'; ?>
    <?php require $templatePath; ?>
</main>

<footer class="footer container">
    <small>Backoffice sécurisé · <?= e(date('Y')) ?></small>
</footer>
</body>
</html>
