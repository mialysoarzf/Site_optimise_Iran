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
        <a href="/" class="text-2xl font-black uppercase tracking-tighter text-[#000000] dark:text-white font-headline">THE DIGITAL LEDGER</a>
        <div class="hidden md:flex gap-6">
            <a class="font-headline tracking-tighter <?= ($currentPath ?? '/') === '/' ? 'text-[#000000] dark:text-white border-b-2 border-[#000000] dark:border-white pb-1 font-bold' : 'text-[#75777d] dark:text-[#c5c6cd] hover:text-[#000000] dark:hover:text-white' ?> text-sm" href="/">INTEL</a>
            <a class="font-headline tracking-tighter <?= ($currentPath ?? '/') === '/articles' ? 'text-[#000000] dark:text-white border-b-2 border-[#000000] dark:border-white pb-1 font-bold' : 'text-[#75777d] dark:text-[#c5c6cd] hover:text-[#000000] dark:hover:text-white' ?> text-sm" href="/articles">ARCHIVE</a>
            <a class="font-headline tracking-tighter <?= ($currentPath ?? '/') === '/a-propos' ? 'text-[#000000] dark:text-white border-b-2 border-[#000000] dark:border-white pb-1 font-bold' : 'text-[#75777d] dark:text-[#c5c6cd] hover:text-[#000000] dark:hover:text-white' ?> text-sm" href="/a-propos">ANALYSIS</a>
            <a class="font-headline tracking-tighter text-[#75777d] dark:text-[#c5c6cd] hover:text-[#000000] dark:hover:text-white text-sm" href="/admin/login">ADMIN</a>
        </div>
    </div>
    <div class="hidden md:block">
        <span class="bg-primary text-on-primary px-5 py-2 text-[10px] font-label font-bold tracking-[0.2em] uppercase">Iran Briefing</span>
    </div>
</nav>

<aside class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-[#f1f3ff] dark:bg-[#101c30] flex flex-col p-6 hidden xl:flex z-40">
    <div class="mb-10">
        <h2 class="font-newsreader italic text-lg text-[#101c30] dark:text-[#e8eeff]">INTEL FEED</h2>
        <p class="font-body uppercase tracking-widest text-[10px] opacity-60">SECTOR: IRAN/CENTCOM</p>
    </div>
    <nav class="flex flex-col gap-2 flex-grow">
        <a class="flex items-center gap-4 p-3 bg-[#000000] text-white dark:bg-[#ffffff] dark:text-[#000000] font-bold font-body uppercase tracking-widest text-xs" href="/">
            <span class="material-symbols-outlined">sensors</span>
            <span>LIVE FEED</span>
        </a>
        <a class="flex items-center gap-4 p-3 text-[#101c30] dark:text-[#f9f9ff] opacity-70 hover:bg-[#e8eeff] dark:hover:bg-[#051125] font-body uppercase tracking-widest text-xs" href="/articles">
            <span class="material-symbols-outlined">rss_feed</span>
            <span>SIGNAL</span>
        </a>
        <a class="flex items-center gap-4 p-3 text-[#101c30] dark:text-[#f9f9ff] opacity-70 hover:bg-[#e8eeff] dark:hover:bg-[#051125] font-body uppercase tracking-widest text-xs" href="/a-propos">
            <span class="material-symbols-outlined">public</span>
            <span>MAPS</span>
        </a>
    </nav>
    <a href="/admin/login" class="mt-auto border border-outline p-4 text-[10px] font-label font-bold tracking-widest text-[#101c30] dark:text-[#f9f9ff] hover:bg-primary hover:text-on-primary transition-colors text-center">DOWNLOAD LEDGER</a>
</aside>

<main class="xl:ml-64 pt-20 min-h-screen">
    <div class="bg-primary text-on-primary py-2 overflow-hidden relative intelligence-ticker-mask">
        <div class="flex whitespace-nowrap gap-12 animate-marquee py-1">
            <span class="flex items-center gap-2 font-label text-[10px] tracking-[0.2em] font-bold"><span class="w-1.5 h-1.5 bg-on-tertiary-container"></span> FLASH: SATELLITE IMAGERY CONFIRMS TROOP MOVEMENT IN KHUZESTAN PROVINCE</span>
            <span class="flex items-center gap-2 font-label text-[10px] tracking-[0.2em] font-bold"><span class="w-1.5 h-1.5 bg-on-tertiary-container"></span> ALERT: CYBER COMMAND DETECTS INCREASED PROBING OF POWER GRID ASSETS</span>
            <span class="flex items-center gap-2 font-label text-[10px] tracking-[0.2em] font-bold"><span class="w-1.5 h-1.5 bg-on-tertiary-container"></span> UPDATE: DIPLOMATIC CHANNELS IN OMAN REPORT STALLED NEGOTIATIONS</span>
        </div>
    </div>
    <?php require $templatePath; ?>
</main>

<footer class="bg-[#000000] text-[#ffffff] w-full py-12 px-6 md:px-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-8">
    <div class="flex flex-col gap-4">
        <span class="font-newsreader text-white text-xl">THE DIGITAL LEDGER</span>
        <p class="font-body text-[10px] tracking-[0.1em] uppercase max-w-md opacity-70">© <?= e((string) date('Y')) ?> DIGITAL LEDGER INTEL GROUP. CLASSIFICATION: UNCLASSIFIED.</p>
    </div>
    <nav class="flex flex-wrap gap-6">
        <a class="font-body text-[10px] tracking-[0.1em] uppercase text-[#c5c6cd] hover:text-white" href="/articles">Archives</a>
        <a class="font-body text-[10px] tracking-[0.1em] uppercase text-[#c5c6cd] hover:text-white" href="/a-propos">Mentions</a>
        <a class="font-body text-[10px] tracking-[0.1em] uppercase text-[#c5c6cd] hover:text-white" href="/">Contact</a>
    </nav>
</footer>
</body>
</html>
