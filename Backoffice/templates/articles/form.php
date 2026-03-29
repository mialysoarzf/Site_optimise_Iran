<?php
$action = $mode === 'edit' ? '/admin/articles/edit/' . (int) ($article['id'] ?? 0) : '/admin/articles/create';
?>
<section class="section-header">
    <h1><?= $mode === 'edit' ? 'Modifier article' : 'Créer article' ?></h1>
    <a class="btn btn-secondary" href="/admin/articles">Retour</a>
</section>

<form class="card form-grid" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

    <label for="title">Titre *</label>
    <input id="title" name="title" required value="<?= e((string) ($article['title'] ?? '')) ?>">

    <label for="slug">Slug *</label>
    <input id="slug" name="slug" required value="<?= e((string) ($article['slug'] ?? '')) ?>">

    <label for="excerpt">Extrait</label>
    <textarea id="excerpt" name="excerpt" rows="3"><?= e((string) ($article['excerpt'] ?? '')) ?></textarea>

    <label for="content">Contenu *</label>
    <textarea id="content" name="content" rows="12" required><?= e((string) ($article['content'] ?? '')) ?></textarea>

    <label for="status">Statut *</label>
    <select id="status" name="status" required>
        <option value="draft" <?= (($article['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Brouillon</option>
        <option value="published" <?= (($article['status'] ?? '') === 'published') ? 'selected' : '' ?>>Publié</option>
    </select>

    <label for="category_id">Catégorie</label>
    <select id="category_id" name="category_id">
        <option value="">-- Aucune --</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= (int) $category['id'] ?>" <?= ((string) ($article['category_id'] ?? '') === (string) $category['id']) ? 'selected' : '' ?>>
                <?= e((string) $category['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <h2>SEO</h2>
    <label for="meta_title">Meta title</label>
    <input id="meta_title" name="meta_title" value="<?= e((string) ($article['meta_title'] ?? '')) ?>">

    <label for="meta_description">Meta description</label>
    <textarea id="meta_description" name="meta_description" rows="3"><?= e((string) ($article['meta_description'] ?? '')) ?></textarea>
    <small class="muted">Longueur recommandée: 140-160 caractères (actuel: <?= mb_strlen((string) ($article['meta_description'] ?? '')) ?>)</small>

    <h2>Image</h2>
    <?php if (!empty($image['url'])): ?>
        <p class="muted">Image actuelle:</p>
        <img src="<?= e((string) $image['url']) ?>" alt="<?= e((string) ($image['alt'] ?? 'Image article')) ?>" class="thumb">
        <label><input type="checkbox" name="remove_image" value="1"> Supprimer l'image actuelle</label>
    <?php endif; ?>

    <label for="image">Uploader une image (jpg/png/webp, 2MB max)</label>
    <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp">

    <label for="image_alt">Texte alternatif image</label>
    <input id="image_alt" name="image_alt" value="<?= e((string) ($article['image_alt'] ?? '')) ?>">

    <div class="actions">
        <button class="btn" type="submit">Enregistrer</button>
    </div>
</form>

<script>
const titleInput = document.getElementById('title');
const slugInput = document.getElementById('slug');
if (titleInput && slugInput) {
  titleInput.addEventListener('input', () => {
    if (slugInput.dataset.touched === '1') return;
    const slug = titleInput.value
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '');
    slugInput.value = slug;
  });
  slugInput.addEventListener('input', () => {
    slugInput.dataset.touched = '1';
  });
}
</script>
