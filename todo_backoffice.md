TODO BACKOFFICE (PHP SANS FRAMEWORK)

OBJECTIF
- Gérer le contenu du site (articles, catégories, images) via une interface admin sécurisée.
- Ne jamais exposer les fichiers .php dans les URLs publiques/admin.

1) ARCHITECTURE DOSSIERS
- Créer une arborescence claire:
  - /public (point d'entrée web)
  - /admin (ou /public/admin)
  - /src (logique métier)
  - /templates (vues)
  - /uploads (images)
- Séparer front et back pour éviter les mélanges de logique.

2) ROUTING + URL REWRITING (ADMIN)
- Activer mod_rewrite (Apache).
- Créer règles admin pour URLs propres:
  - /admin/login
  - /admin/dashboard
  - /admin/articles
  - /admin/articles/create
  - /admin/articles/edit/{id}
  - /admin/articles/delete/{id}
  - /admin/categories
- Masquer totalement les extensions .php dans les URLs.
- Rediriger proprement les anciennes URLs .php vers URLs clean (301 si nécessaire).

3) AUTHENTIFICATION ADMIN
- Écran login (username + password).
- Vérification mot de passe via hash (password_verify).
- Session sécurisée après connexion (session_regenerate_id).
- Logout propre (destroy session + cookie session).
- Verrouillage de toutes routes /admin/* hors /admin/login.

4) GESTION DES ROLES
- Contrôler le rôle user (admin/editor).
- Limiter actions critiques (suppression, publication) selon rôle.

5) DASHBOARD
- Compteurs:
  - total articles
  - brouillons
  - publiés
  - catégories
- Liste des derniers articles modifiés.

6) MODULE ARTICLES (CRUD)
- Liste paginée + recherche par titre.
- Formulaire création:
  - title, slug, excerpt, content
  - status (draft/published)
  - category
  - meta_title, meta_description
- Formulaire édition des mêmes champs.
- Suppression avec confirmation.
- Validation serveur:
  - title/content obligatoires
  - slug unique
  - status valide
- Génération slug auto depuis le titre (modifiable manuellement).

7) MODULE CATEGORIES (CRUD)
- Liste catégories.
- Création/édition/suppression.
- Slug unique obligatoire.
- Empêcher suppression si impact non géré (ou passer category_id à NULL).

8) MODULE IMAGES
- Upload image pour article.
- Validation type/taille (jpg/png/webp).
- Sauvegarder chemin + alt dans table images.
- Associer image à article.
- Suppression fichier + enregistrement DB.

9) REGLES DE PUBLICATION
- draft = invisible en front.
- published = visible en front.
- Si publication différée ajoutée plus tard: prévoir published_at.

10) SECURITE MINIMALE
- Requêtes préparées partout (PDO).
- Validation/sanitization de toutes entrées.
- Protection CSRF sur formulaires POST.
- Échappement HTML en sortie (anti XSS).
- Messages d'erreurs non sensibles (pas de stack trace en prod).

11) UX BACKOFFICE
- Messages flash (succès/erreur).
- Formulaires pré-remplis en cas d'erreur.
- Boutons explicites (Enregistrer / Publier / Supprimer).

12) SEO COTE ADMIN (DONNEES)
- Exiger meta_title/meta_description sur publication.
- Prévisualisation snippet SEO (optionnel mais utile).
- Alerte si longueur meta non optimale.

13) TESTS FONCTIONNELS A FAIRE
- Login valide/invalide.
- Accès non connecté à /admin/* interdit.
- Création article draft puis publication.
- Slug dupliqué refusé.
- Upload image invalide refusé.
- Suppression article supprime images liées (cascade DB + fichiers).

15) PREPARATION LIVRABLE
- Fichier SQL importable.
- Compte admin seed (username: user) avec mot de passe hashé.
- README avec:
  - URL admin
  - identifiants démo
  - étapes de lancement Docker
