<?php
$action = $mode === 'edit'
  ? '/categories/edit/' . (int) ($category['id'] ?? 0)
  : '/categories/create';
?>
<section class="section-header">
    <h1><?= $mode === 'edit' ? 'Modifier catégorie' : 'Créer catégorie' ?></h1>
  <a class="btn btn-secondary" href="/categories">Retour</a>
</section>

<form class="card form-grid category-form" method="post" action="<?= e($action) ?>">
    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

  <div class="form-row">
    <div class="field">
      <label for="name">Nom *</label>
      <input id="name" name="name" required placeholder="Ex: Conflit Iran 2026" value="<?= e((string) ($category['name'] ?? '')) ?>">
    </div>

    <div class="field">
      <label for="slug">Slug *</label>
      <input id="slug" name="slug" required placeholder="Ex: conflit-iran-2026" value="<?= e((string) ($category['slug'] ?? '')) ?>">
    </div>
  </div>

  <div class="actions field-full">
    <button type="submit" class="btn">Enregistrer</button>
  </div>
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
