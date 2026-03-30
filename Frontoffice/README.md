# Frontoffice - Site d'informations (guerre en Iran)

## URL du frontoffice

- <http://localhost:8080/>

## URLs disponibles

- `/`
- `/articles`
- `/article/{slug}`
- `/categorie/{slug}`
- `/a-propos`

## Fonctionnalités implémentées

- Routing front sans `.php` via `mod_rewrite`
- Redirection `.php` vers URL clean
- Architecture PHP simple (procédurale): `index.php` + `app.php`
- Affichage **uniquement** des articles `published`
- Tri descendant par date (`updated_at` puis `created_at`)
- Pagination (10 articles/page) pour listes globales et catégories
- Page détail article via slug avec galerie d'images (`alt` obligatoire en DB)
- SEO technique: `title`, `meta description`, structure de titres, H1 unique/page
- 404 dédiée avec code HTTP 404
- Sécurité: échappement HTML (`htmlspecialchars`), requêtes préparées, validation slug

## Vérification Lighthouse (mobile + desktop)

1. Lancer `docker-compose up -d`
2. Importer `base.sql` dans PostgreSQL
3. Ouvrir `http://localhost:8080/` puis tester Lighthouse
4. Vérifier les priorités: SEO, Performance, Best Practices, Accessibility
