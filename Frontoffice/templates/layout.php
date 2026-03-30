<!doctype html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Iran Infos') ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'Site d’informations sur la guerre en Iran.') ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'on-tertiary-container': '#f0443a',
                        'on-surface-variant': '#45474d',
                        'on-surface': '#101c30',
                        'outline-variant': '#c5c6cd',
                        'surface-container-lowest': '#ffffff',
                        'surface-container-low': '#f1f3ff',
                        'surface-variant': '#d7e2ff',
                        'outline': '#75777d',
                        'surface': '#f9f9ff',
                        'primary-container': '#101c30',
                        'surface-container': '#e8eeff',
                        'background': '#f9f9ff',
                        'primary': '#000000',
                        'on-primary': '#ffffff',
                    },
                    fontFamily: {
                        headline: ['Newsreader', 'serif'],
                        body: ['Public Sans', 'sans-serif'],
                        label: ['Public Sans', 'sans-serif'],
                        newsreader: ['Newsreader', 'serif']
                    },
                    borderRadius: { DEFAULT: '0px', lg: '0px', xl: '0px', full: '9999px' }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="/public/assets/style.css">
</head>
<body class="bg-surface text-on-surface font-body selection:bg-primary selection:text-on-primary">
<nav class="bg-[#f9f9ff] dark:bg-[#051125] text-[#101c30] dark:text-[#f9f9ff] shadow-[0px_12px_32px_rgba(16,28,48,0.06)] fixed top-0 z-50 w-full px-6 md:px-10 py-4 flex justify-between items-center max-w-none">
    <div class="flex items-center gap-8">
        <a href="/" class="text-2xl font-black uppercase tracking-tighter text-[#000000] dark:text-white font-headline">IRAN INFOS</a>
        <div class="hidden md:flex gap-6">
            <a class="font-headline tracking-tighter <?= ($currentPath ?? '/') === '/' ? 'text-[#000000] dark:text-white border-b-2 border-[#000000] dark:border-white pb-1 font-bold' : 'text-[#75777d] dark:text-[#c5c6cd] hover:text-[#000000] dark:hover:text-white' ?> text-sm" href="/">DERNIÈRES INFOS</a>
            <a class="font-headline tracking-tighter <?= ($currentPath ?? '/') === '/articles' ? 'text-[#000000] dark:text-white border-b-2 border-[#000000] dark:border-white pb-1 font-bold' : 'text-[#75777d] dark:text-[#c5c6cd] hover:text-[#000000] dark:hover:text-white' ?> text-sm" href="/articles">ARTICLES</a>
            <a class="font-headline tracking-tighter <?= ($currentPath ?? '/') === '/a-propos' ? 'text-[#000000] dark:text-white border-b-2 border-[#000000] dark:border-white pb-1 font-bold' : 'text-[#75777d] dark:text-[#c5c6cd] hover:text-[#000000] dark:hover:text-white' ?> text-sm" href="/a-propos">CONTEXTE</a>
        </div>
    </div>
    <div class="hidden md:block">
        <span class="bg-primary text-on-primary px-5 py-2 text-[10px] font-label font-bold tracking-[0.2em] uppercase">Suivi du conflit</span>
    </div>
</nav>

<main class="pt-20 min-h-screen">
    <div class="bg-primary text-on-primary py-2 overflow-hidden relative intelligence-ticker-mask">
        <div class="flex whitespace-nowrap gap-12 animate-marquee py-1">
            <?php if (!empty($tickerItems)): ?>
                <?php foreach ($tickerItems as $ticker): ?>
                    <a class="flex items-center gap-2 font-label text-[10px] tracking-[0.2em] font-bold hover:underline" href="/article/<?= e((string) $ticker['slug']) ?>">
                        <span class="w-1.5 h-1.5 bg-on-tertiary-container"></span>
                        <?= e((string) $ticker['title']) ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="flex items-center gap-2 font-label text-[10px] tracking-[0.2em] font-bold"><span class="w-1.5 h-1.5 bg-on-tertiary-container"></span> Dernières publications en cours de chargement</span>
            <?php endif; ?>
        </div>
    </div>
    <?php require $templatePath; ?>
</main>

<footer class="bg-[#000000] text-[#ffffff] w-full py-12 px-6 md:px-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-8">
    <div class="flex flex-col gap-4">
        <span class="font-newsreader text-white text-xl">IRAN INFOS</span>
        <p class="font-body text-[10px] tracking-[0.1em] uppercase max-w-md opacity-70">© <?= e((string) date('Y')) ?> Iran Infos · veille éditoriale sur le conflit en Iran.</p>
    </div>
    <nav class="flex flex-wrap gap-6">
        <a class="font-body text-[10px] tracking-[0.1em] uppercase text-[#c5c6cd] hover:text-white" href="/articles">Articles</a>
        <a class="font-body text-[10px] tracking-[0.1em] uppercase text-[#c5c6cd] hover:text-white" href="/a-propos">À propos</a>
        <a class="font-body text-[10px] tracking-[0.1em] uppercase text-[#c5c6cd] hover:text-white" href="/">Accueil</a>
    </nav>
</footer>
</body>
</html>
