-- Seed articles: catégorie "Guerre Iran 2026"
-- Exécutable plusieurs fois (idempotent sur les slugs)

BEGIN;

INSERT INTO categories (name, slug)
VALUES ('Guerre Iran 2026', 'guerre-iran-2026')
ON CONFLICT (slug) DO UPDATE SET name = EXCLUDED.name;

WITH cat AS (
    SELECT id
    FROM categories
    WHERE slug = 'guerre-iran-2026'
    LIMIT 1
), author_row AS (
    SELECT id
    FROM users
    ORDER BY id ASC
    LIMIT 1
)
INSERT INTO articles (
    title,
    slug,
    content,
    excerpt,
    category_id,
    author_id,
    status,
    meta_title,
    meta_description,
    published_at
)
SELECT
    x.title,
    x.slug,
    x.content,
    x.excerpt,
    cat.id,
    author_row.id,
    'published'::article_status,
    x.meta_title,
    x.meta_description,
    NOW()
FROM cat
CROSS JOIN author_row
CROSS JOIN (
    VALUES
    (
        'Détails sur les frappes à Téhéran',
        'details-frappes-teheran',
        E'Tôt ce mardi matin, la capitale iranienne a été secouée par une série de déflagrations majeures. Selon les agences de presse locales comme Fars et Tasnim, les explosions ont été entendues distinctement dans les secteurs est et ouest de la ville.\n\nCibles stratégiques : L\'armée israélienne a confirmé avoir visé une « infrastructure militaire » spécifique. Des rapports ultérieurs indiquent qu\'une sous-station d\'une centrale énergétique a été directement touchée, ce qui explique l\'ampleur des pannes de courant.\n\nAvertissements aux civils : Fait notable, l\'armée israélienne avait diffusé des messages sur les réseaux sociaux (notamment Telegram) quelques minutes avant l\'attaque, exhortant les habitants de certains quartiers résidentiels à rejoindre les abris immédiatement pour minimiser les pertes civiles.\n\nImpact sur la population : Le témoignage de Shahrzad, une habitante de Téhéran de 39 ans, illustre le climat de terreur : elle décrit une vie désormais rythmée par la peur constante des explosions et de la mort, loin du quotidien ordinaire d\'avant le conflit.',
        'La capitale iranienne a été touchée par des frappes ciblant des infrastructures militaires et énergétiques, dans un climat de forte tension.',
        'Détails sur les frappes à Téhéran - Guerre Iran 2026',
        'Synthèse des frappes à Téhéran, des cibles visées et des impacts civils dans le contexte de la guerre Iran 2026.'
    ),
    (
        'Escalade énergétique et menaces de Donald Trump',
        'escalade-energetique-menaces-trump',
        E'La journée de lundi a été marquée par une pression diplomatique et militaire sans précédent de la part des États-Unis.\n\nL\'ultimatum sur le pétrole : Le président Donald Trump a durci le ton, menaçant d\'utiliser la force pour « anéantir » les sites énergétiques iraniens si le détroit d\'Ormuz n\'était pas rouvert immédiatement. Parmi les cibles potentielles citées sur son réseau Truth Social, on retrouve l\'île de Kharg (centre névralgique des exportations de brut), des puits de pétrole et même des usines de dessalement d\'eau.\n\nL\'option terrestre : Pour la première fois de manière aussi explicite, l\'administration américaine a évoqué une possible opération au sol pour s\'emparer du terminal pétrolier de Kharg, utilisant l\'expression « prendre le pétrole » pour paralyser financièrement le régime.\n\nRéaction iranienne : Malgré ces menaces, le ministre iranien des Affaires étrangères, Abbas Araghchi, a maintenu une ligne dure, appelant les pays voisins (comme l\'Arabie saoudite) à expulser les forces américaines de la région, affirmant que les frappes de Téhéran ne sont que des réponses aux « agresseurs ennemis ».',
        'Washington hausse la pression sur les infrastructures énergétiques iraniennes, tandis que Téhéran maintient une position de confrontation.',
        'Escalade énergétique et menaces de Donald Trump - Guerre Iran 2026',
        'Analyse de la pression américaine sur le pétrole iranien et des réactions diplomatiques régionales.'
    )
) AS x(title, slug, content, excerpt, meta_title, meta_description)
ON CONFLICT (slug) DO UPDATE
SET
    title = EXCLUDED.title,
    content = EXCLUDED.content,
    excerpt = EXCLUDED.excerpt,
    category_id = EXCLUDED.category_id,
    author_id = EXCLUDED.author_id,
    status = EXCLUDED.status,
    meta_title = EXCLUDED.meta_title,
    meta_description = EXCLUDED.meta_description,
    published_at = EXCLUDED.published_at,
    updated_at = NOW();

COMMIT;
