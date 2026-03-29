# Guide de test du Backoffice

## 1) Importer les données de base

1. Lancer les conteneurs:
   - `docker-compose up -d`
2. Importer le schéma principal:
   - `base.sql`
3. Importer les données de démonstration:
   - [Backoffice/demo_seed.sql](demo_seed.sql)

Compte admin:

- Username: `user`
- Password: `pass`

URL:

- <http://localhost:8081/admin/login>

---

## 2) Comment tester rapidement

### A. Connexion

- Ouvrir `/admin/login`
- Se connecter avec `user` / `pass`
- Vérifier la redirection vers `/admin/dashboard`

### B. Vérifier les données injectées

- Aller dans `Catégories`:
  - `Conflit Iran 2026` / `conflit-iran-2026`
  - `Diplomatie internationale` / `diplomatie-internationale`

- Aller dans `Articles`:
  - 1 article `published`
  - 1 article `draft`

---

## 3) Exemple simple : créer une catégorie

Dans `Catégories > Créer une catégorie`:

- Nom: `Aide humanitaire`
- Slug: `aide-humanitaire`

Règles:

- Le `slug` doit être unique
- Minuscules + tirets recommandés

Si le slug existe déjà, le backoffice refuse l'enregistrement.

---

## 4) Exemple simple : créer un article

Dans `Articles > Créer un article`:

- Titre: `Bilan hebdomadaire de la situation en Iran`
- Slug: `bilan-hebdomadaire-situation-iran`
- Extrait: `Résumé des faits marquants de la semaine.`
- Contenu:
  - `<h2>Situation générale</h2><p>Le contexte évolue rapidement...</p>`
  - `<h2>Points clés</h2><p>Éléments principaux à retenir...</p>`
- Statut:
  - `draft` = non visible côté front
  - `published` = visible côté front
- Catégorie: `Conflit Iran 2026`
- Meta title: `Bilan hebdomadaire Iran - Analyse`
- Meta description: `Analyse hebdomadaire de la situation en Iran, enjeux et évolutions.`
- Image (optionnel): jpg/png/webp (2MB max)
- Alt image: `Photo d'illustration de la situation en Iran`

Important:

- `title` et `content` sont obligatoires
- `slug` doit être unique
- Pour publier (`published`), `meta_title` et `meta_description` sont requis

---

## 5) Cycle de test conseillé (très concret)

1. Créer une catégorie (ex: `Aide humanitaire`)
2. Créer un article en `draft`
3. Vérifier qu'il apparaît dans la liste admin
4. Éditer l'article et passer en `published`
5. Vérifier les messages de succès
6. Tester un slug dupliqué pour voir l'erreur
7. Tester un upload invalide (ex: PDF) pour voir le blocage

---

## 6) En cas de reset complet

Ordre recommandé:

1. Importer [base.sql](../base.sql)
2. Importer [Backoffice/demo_seed.sql](demo_seed.sql)
3. Se reconnecter sur <http://localhost:8081/admin/login>
