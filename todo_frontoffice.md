TODO FRONTOFFICE (PHP SANS FRAMEWORK)

OBJECTIF
- Afficher les contenus publiés avec URLs propres, SEO correct, et navigation claire.
- Masquer totalement la techno (pas de .php visible dans les URLs).

1) ROUTING + URL REWRITING (FRONT)
- Activer mod_rewrite.
- Définir routes propres:
  - /
  - /articles
  - /article/{slug}
  - /categorie/{slug}
  - /a-propos (si page statique)
- Rediriger URLs avec .php vers URLs clean.
- Gérer page 404 propre pour URL inconnue.

2) REGLE D'AFFICHAGE CONTENU
- N'afficher que les articles status='published'.
- Trier les listes par date DESC (updated_at ou created_at).
- Gérer pagination (ex: 10 articles/page).

3) PAGE ACCUEIL
- Mettre en avant les derniers articles publiés.
- Section catégories principales.
- Carte article avec:
  - titre
  - extrait
  - date
  - image principale (si dispo)

4) LISTE ARTICLES
- Afficher cartes d'articles (titre, extrait, date, catégorie).
- Lien vers détail via slug.

5) DETAIL ARTICLE
- Charger article par slug.
- Afficher:
  - h1 = titre
  - date publication
  - catégorie
  - contenu
  - image(s) avec alt obligatoire
- Afficher articles liés (même catégorie) en bas (optionnel).

6) LISTE PAR CATEGORIE
- URL /categorie/{slug}
- Filtrer articles publiés par catégorie.
- Pagination.

7) SEO TECHNIQUE
- Balise <title>:
  - article: meta_title sinon title
  - liste/catégorie: titre de page explicite
- Meta description:
  - article: meta_description sinon excerpt tronqué
- Un seul h1 par page.
- Structure sémantique h2/h3 dans templates.
- Images toujours avec alt.
- URL lisibles, minuscules, tirets.

8) PERFORMANCE BASIQUE
- Optimiser poids images (webp/jpg compressé).
- Lazy loading images (loading="lazy").
- Minifier CSS/JS (si possible).

9) PAGE 404 + ERREURS
- Créer template 404 clair + lien retour accueil.
- Retour 404 HTTP correct.
- Ne pas exposer erreurs SQL/PHP en production.

10) SECURITE FRONT
- Échapper toutes sorties dynamiques (htmlspecialchars).
- Requêtes préparées.
- Validation des paramètres URL (slug).

11) UI/UX MINIMUM
- Header avec navigation claire.
- Footer avec mentions/contact.
- Design responsive mobile/desktop.
- Lisibilité (contraste, tailles de police correctes).

12) LIGHTHOUSE (EXIGENCE SUJET)
- Tester local en mobile + desktop.
- Corriger priorités:
  - SEO
  - Performance
  - Best Practices
  - Accessibility

13) CHECKLIST FINALE AVANT LIVRAISON
- Toutes URLs sans .php.
- Aucune page front non SEO-friendly.
- Tous les articles publiés visibles, drafts invisibles.
- Toutes images avec alt.
- 404 fonctionnelle.
- Site fonctionnel en Docker.
