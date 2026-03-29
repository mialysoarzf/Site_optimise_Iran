# Guide de test du Backoffice

## 1) Accès

- URL: <http://localhost:8081/admin/login>
- Username: `user`
- Password: `pass`

## 2) Test rapide

1. Ouvrir `/admin/login`.
1. Se connecter avec `user` / `pass`.
1. Vérifier la redirection vers `/admin/dashboard`.
1. Aller dans `Catégories` et vérifier la présence de `Conflit Iran 2026` (`conflit-iran-2026`) et `Diplomatie internationale` (`diplomatie-internationale`).
1. Aller dans `Articles` et vérifier qu'il y a 2 articles `published`.

## 3) Exemple catégorie

Dans `Catégories > Créer une catégorie`:

- Nom: `Aide humanitaire`
- Slug: `aide-humanitaire`

Règles:

- Le `slug` doit être unique.
- Utiliser de préférence minuscules + tirets.

## 4) Exemple article

Dans `Articles > Créer un article`:

- Titre: `Bilan hebdomadaire de la situation en Iran`
- Slug: `bilan-hebdomadaire-situation-iran`
- Extrait: `Résumé des faits marquants de la semaine.`
- Contenu:
  - `<h2>Situation générale</h2><p>Le contexte évolue rapidement...</p>`
  - `<h2>Points clés</h2><p>Éléments principaux à retenir...</p>`
- Statut: `published`
- Catégorie: `Conflit Iran 2026`
- Meta title: `Bilan hebdomadaire Iran - Analyse`
- Meta description: `Analyse hebdomadaire de la situation en Iran, enjeux et évolutions.`
- Alt image: `Photo d'illustration de la situation en Iran`

Important:

- `title` et `content` sont obligatoires.
- `slug` doit être unique.
- Pour publier (`published`), `meta_title` et `meta_description` sont requis.

## 5) Où changer le statut (`draft` / `published`)

1. Aller dans `Articles`.
1. Cliquer `Modifier` sur l'article.
1. Dans `Statut`, choisir `published`.
1. Cliquer `Enregistrer`.

## 6) Reset propre avec accents UTF-8

Pour éviter les textes cassés (`d'??tape`), importer les SQL via fichiers dans le conteneur DB.

1. Copier les SQL:

  `docker cp .\base.sql site-informatif-iran-db:/tmp/base.sql`

  `docker cp .\Backoffice\demo_seed.sql site-informatif-iran-db:/tmp/demo_seed.sql`

1. Exécuter les imports:

  `docker-compose exec -T db psql -U app_user -d app_db -f /tmp/base.sql`

  `docker-compose exec -T db psql -U app_user -d app_db -f /tmp/demo_seed.sql`

1. Se reconnecter sur <http://localhost:8081/admin/login>.
