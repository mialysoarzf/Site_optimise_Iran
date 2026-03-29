-- Données de démonstration Backoffice (PostgreSQL)
-- Exécuter APRÈS base.sql

SET client_encoding = 'UTF8';

BEGIN;

-- 1) Catégories
INSERT INTO categories (name, slug)
VALUES
  ('Conflit Iran 2026', 'conflit-iran-2026'),
  ('Diplomatie internationale', 'diplomatie-internationale'),
  ('Économie & énergie', 'economie-energie')
ON CONFLICT (slug) DO UPDATE
SET name = EXCLUDED.name;

-- 2) Articles
INSERT INTO articles (
  title,
  slug,
  excerpt,
  content,
  category_id,
  author_id,
  status,
  meta_title,
  meta_description,
  published_at
)
VALUES
(
  'Chronologie du conflit Iran 2026 : les dates clés',
  'chronologie-conflit-iran-2026',
  U&'R\00E9sum\00E9 des \00E9v\00E9nements majeurs des derni\00E8res semaines.',
  $$
<h2>Contexte</h2>
<p>Ce dossier présente les faits principaux du conflit en Iran.</p>
<h2>Faits marquants</h2>
<p>Les événements sont présentés de manière chronologique pour faciliter la lecture.</p>
<h3>Impact régional</h3>
<p>Les conséquences touchent la sécurité, la diplomatie et l'économie.</p>
  $$,
  (SELECT id FROM categories WHERE slug = 'conflit-iran-2026' LIMIT 1),
  (SELECT id FROM users WHERE username = 'user' LIMIT 1),
  'published',
  'Chronologie du conflit Iran 2026 - Analyse complète',
  'Chronologie claire du conflit en Iran : dates clés, contexte et conséquences régionales.',
  NOW()
),
(
  'Négociations diplomatiques : où en est-on ?',
  'negociations-diplomatiques-ou-en-est-on',
  U&'Point d''\00E9tape sur les discussions internationales en cours.',
  $$
<h2>État des discussions</h2>
<p>Plusieurs acteurs diplomatiques participent aux pourparlers.</p>
<h2>Scénarios possibles</h2>
<p>Le scénario de désescalade reste dépendant des engagements sur le terrain.</p>
  $$,
  (SELECT id FROM categories WHERE slug = 'diplomatie-internationale' LIMIT 1),
  (SELECT id FROM users WHERE username = 'user' LIMIT 1),
  'published',
  'Négociations diplomatiques Iran - Situation actuelle',
  U&'Analyse des discussions diplomatiques autour du conflit en Iran.',
  NOW()
)
ON CONFLICT (slug) DO UPDATE
SET
  title = EXCLUDED.title,
  excerpt = EXCLUDED.excerpt,
  content = EXCLUDED.content,
  category_id = EXCLUDED.category_id,
  author_id = EXCLUDED.author_id,
  status = EXCLUDED.status,
  meta_title = EXCLUDED.meta_title,
  meta_description = EXCLUDED.meta_description,
  published_at = EXCLUDED.published_at,
  updated_at = NOW();

-- 3) Image pour l'article publié (URL de démonstration)
INSERT INTO images (article_id, url, alt)
VALUES (
  (SELECT id FROM articles WHERE slug = 'chronologie-conflit-iran-2026' LIMIT 1),
  '/uploads/demo-iran.webp',
  'Carte de la région et zones de tension en Iran'
)
ON CONFLICT DO NOTHING;

COMMIT;
