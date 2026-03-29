<?php
$action = $mode === 'edit' ? '/admin/categories/edit/' . (int) ($category['id'] ?? 0) : '/admin/categories/create';
?>
<section class="section-header">
    <h1><?= $mode === 'edit' ? 'Modifier catégorie' : 'Créer catégorie' ?></h1>
    <a class="btn btn-secondary" href="/admin/categories">Retour</a>
</section>

<form class="card form-grid" method="post" action="<?= e($action) ?>">
    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

    <label for="name">Nom *</label>
    <input id="name" name="name" required value="<?= e((string) ($category['name'] ?? '')) ?>">

    <label for="slug">Slug *</label>
    <input id="slug" name="slug" required value="<?= e((string) ($category['slug'] ?? '')) ?>">

    <button type="submit" class="btn">Enregistrer</button>
</form>

<script>
const nameInput = document.getElementById('name');
const slugInput = document.getElementById('slug');
if (nameInput && slugInput) {
  nameInput.addEventListener('input', () => {
    if (slugInput.dataset.touched === '1') return;
    const slug = nameInput.value
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
