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
    ),
    (
        'Téhéran dans le noir : sabotage des infrastructures et agonie du réseau G-42',
        'teheran-noir-sabotage-g42',
        E'La stratégie de « pression maximale » s\'est déplacée vers les services de base. Les frappes du 31 mars ont spécifiquement ciblé la station électrique G-42, le cœur énergétique de l\'est de la capitale. Résultat : plus de 4 millions de civils sont privés d\'électricité et d\'eau courante.\n\nLes hôpitaux, dont l\'établissement Gandhi, fonctionnent sur des générateurs de secours dont le carburant s\'épuise. Les autorités locales rapportent que la pression dans le réseau de gaz domestique a chuté de 40 %, rendant le chauffage impossible alors que les températures nocturnes frôlent encore les 5°C.',
        'Les frappes du 31 mars ont plongé l''est de Téhéran dans le noir et fragilisé les services essentiels.',
        'Téhéran dans le noir : sabotage du réseau G-42 - Guerre Iran 2026',
        'Focus sur le sabotage du réseau G-42 et ses conséquences sur les services vitaux à Téhéran.'
    ),
    (
        'Le blocus du détroit d''Ormuz : 20 % du pétrole mondial pris en otage',
        'blocus-detroit-ormuz-petrole-otage',
        E'Depuis le début du mois de mars, le détroit d\'Ormuz est devenu un cimetière de navires. L\'Iran a déployé des milliers de mines marines « intelligentes » et utilise des essaims de drones kamikazes pour harceler les pétroliers.\n\nCette obstruction paralyse environ 21 millions de barils par jour. En réponse, la marine américaine a lancé l\'opération de déminage « Sentinel », mais les pertes sont lourdes : trois dragueurs de mines ont déjà été touchés.\n\nLe coût des assurances maritimes a été multiplié par dix, forçant les transporteurs à contourner l\'Afrique, augmentant les délais de livraison de 15 jours pour le GNL qatari.',
        'Le blocus d''Ormuz paralyse 20 % du pétrole mondial et déclenche une riposte navale américaine.',
        'Blocus du détroit d''Ormuz - Guerre Iran 2026',
        'Impact du blocus d''Ormuz sur les flux énergétiques et la logistique maritime.'
    ),
    (
        'Bilan humanitaire au 31 mars : une nation au bord de l''abîme',
        'bilan-humanitaire-31-mars',
        E'Après 32 jours de combats acharnés, le coût humain est vertigineux. Les estimations croisées des ONG et des services de renseignement font état d\'une fourchette allant de 3 500 à 7 700 décès, incluant un nombre croissant de civils pris au piège dans les zones urbaines.\n\nOn dénombre plus de 25 000 blessés et environ 1,2 million de déplacés internes fuyant les côtes et les centres industriels. La destruction des usines de dessalement d\'eau dans le sud du pays fait craindre une crise sanitaire majeure d\'ici la mi-avril si aucun corridor humanitaire n\'est ouvert.',
        'Le bilan humain grimpe et la crise humanitaire s''aggrave avec des déplacements massifs.',
        'Bilan humanitaire au 31 mars - Guerre Iran 2026',
        'Synthèse des pertes humaines et des risques sanitaires liés aux combats.'
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

WITH article_rows AS (
    SELECT id, slug
    FROM articles
    WHERE slug IN (
        'details-frappes-teheran',
        'escalade-energetique-menaces-trump',
        'teheran-noir-sabotage-g42',
        'blocus-detroit-ormuz-petrole-otage',
        'bilan-humanitaire-31-mars'
    )
), img_data(slug, url, alt) AS (
    VALUES
        (
            'details-frappes-teheran',
            '/uploads/frappes-teheran.jpg',
            'Panache de fumée au-dessus d''installations près de Téhéran'
        ),
        (
            'escalade-energetique-menaces-trump',
            '/uploads/escalade-trump.jpg',
            'Donald Trump évoque une escalade contre les infrastructures énergétiques'
        ),
        (
            'teheran-noir-sabotage-g42',
            '/uploads/teheran-g42.jpg',
            'Réseau électrique de Téhéran après les frappes sur G-42'
        ),
        (
            'blocus-detroit-ormuz-petrole-otage',
            '/uploads/ormuz-blocus.jpg',
            'Navire pétrolier près du détroit d''Ormuz'
        ),
        (
            'bilan-humanitaire-31-mars',
            '/uploads/bilan-humanitaire.jpg',
            'Population déplacée et aide humanitaire dans les zones touchées'
        )
), deleted AS (
    DELETE FROM images i
    USING article_rows a
    WHERE i.article_id = a.id
    RETURNING i.id
)
INSERT INTO images (article_id, url, alt)
SELECT a.id, d.url, d.alt
FROM article_rows a
JOIN img_data d ON d.slug = a.slug;

COMMIT;
