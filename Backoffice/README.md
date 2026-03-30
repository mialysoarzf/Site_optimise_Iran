# Backoffice - Site d'informations (guerre en Iran)

## URL du backoffice

- <http://localhost:8081/login>

## Compte admin seed

- username: `user`
- mot de passe: `pass`

## Fonctionnalités implémentées

- Authentification admin sécurisée (`password_verify`, session régénérée)
- Navigation PHP classique (pages et traitements `.php`)
- URLs réécrites sans `.php` (ex: `/articles/edit/1`, `/categories/edit/2`)
- CRUD Articles regroupé dans un seul point d'entrée (`articlesCrud.php`)
- CRUD Catégories regroupé dans un seul point d'entrée (`categoriesCrud.php`)
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
3. Ouvrir <http://localhost:8081/login>.
