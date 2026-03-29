# Backoffice - Site d'informations (guerre en Iran)

## URL du backoffice

- <http://localhost:8081/admin/login>

## Compte admin seed

- username: `user`
- mot de passe: `pass`

## Fonctionnalités implémentées

- Authentification admin sécurisée (`password_verify`, session régénérée)
- Routes propres sans `.php` via `mod_rewrite`
- Dashboard avec compteurs + derniers articles modifiés
- CRUD articles (pagination + recherche + slug unique + statut)
- CRUD catégories (slug unique, suppression avec détachement des articles)
- Upload images article (jpg/png/webp, taille max 2MB)
- Suppression des images en base et sur disque
- Protection CSRF sur formulaires POST
- Requêtes préparées PDO partout
- Échappement HTML en sortie
- Messages flash succès/erreur
- UI responsive avec la police **Inter**

## Démarrage

1. `docker-compose up -d`
2. Importer `base.sql` dans PostgreSQL.
3. Ouvrir <http://localhost:8081/admin/login>.
