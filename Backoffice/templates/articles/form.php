<?php
$action = $mode === 'edit'
  ? '/articles/edit/' . (int) ($article['id'] ?? 0)
  : '/articles/create';
?>
<section class="section-header">
    <h1><?= $mode === 'edit' ? 'Modifier article' : 'Créer article' ?></h1>
  <a class="btn btn-secondary" href="/articles">Retour</a>
</section>

<form class="card form-grid article-form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

  <div class="form-row">
    <div class="field">
      <label for="title">Titre *</label>
      <input id="title" name="title" required placeholder="Ex: Chronologie du conflit Iran 2026" value="<?= e((string) ($article['title'] ?? '')) ?>">
    </div>

    <div class="field">
      <label for="slug">Slug *</label>
      <input id="slug" name="slug" required placeholder="Ex: chronologie-conflit-iran-2026" value="<?= e((string) ($article['slug'] ?? '')) ?>">
    </div>
  </div>

  <div class="field field-full">
    <label for="excerpt">Extrait</label>
    <textarea id="excerpt" name="excerpt" rows="3" placeholder="Ex: Résumé des événements majeurs des dernières semaines."><?= e((string) ($article['excerpt'] ?? '')) ?></textarea>
  </div>

  <div class="field field-full">
    <label for="content">Contenu *</label>
    <textarea id="content" name="content" rows="12" placeholder="Ex: ajoutez ici le contenu structuré de l'article (titres, sous-sections, paragraphes).\n\nTitre section\nSous-section\nTexte..."><?= e((string) ($article['content'] ?? '')) ?></textarea>
  </div>

  <div class="form-row">
    <div class="field">
      <label for="status">Statut *</label>
      <select id="status" name="status" required>
        <option value="draft" <?= (($article['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Brouillon</option>
        <option value="published" <?= (($article['status'] ?? '') === 'published') ? 'selected' : '' ?>>Publié</option>
      </select>
    </div>

    <div class="field">
      <label for="category_id">Catégorie</label>
      <select id="category_id" name="category_id">
        <option value="">-- Aucune --</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= (int) $category['id'] ?>" <?= ((string) ($article['category_id'] ?? '') === (string) $category['id']) ? 'selected' : '' ?>>
            <?= e((string) $category['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <h2 class="field-full section-title">SEO</h2>
  <div class="form-row">
    <div class="field">
      <label for="meta_title">Meta title</label>
      <input id="meta_title" name="meta_title" placeholder="Ex: Iran 2026 - Chronologie et points clés" value="<?= e((string) ($article['meta_title'] ?? '')) ?>">
    </div>

    <div class="field">
      <label for="meta_description">Meta description</label>
      <textarea id="meta_description" name="meta_description" rows="3" placeholder="Ex: Analyse chronologique du conflit en Iran, acteurs impliqués et impacts régionaux."><?= e((string) ($article['meta_description'] ?? '')) ?></textarea>
      <small class="muted">Longueur recommandée: 140-160 caractères (actuel: <?= mb_strlen((string) ($article['meta_description'] ?? '')) ?>)</small>
    </div>
  </div>

  <h2 class="field-full section-title">Image</h2>
    <?php if (!empty($image['url'])): ?>
    <div class="field field-full image-current">
      <div>
        <p class="muted">Image actuelle :</p>
        <img src="<?= e((string) $image['url']) ?>" alt="<?= e((string) ($image['alt'] ?? 'Image article')) ?>" class="thumb">
      </div>
      <label><input type="checkbox" name="remove_image" value="1"> Supprimer l'image actuelle</label>
    </div>
    <?php endif; ?>

  <div class="form-row">
    <div class="field">
      <label for="image">Uploader une image (jpg/png/webp, 2MB max)</label>
      <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
    </div>

    <div class="field">
      <label for="image_alt">Texte alternatif image</label>
      <input id="image_alt" name="image_alt" placeholder="Ex: Carte des zones de tension en Iran" value="<?= e((string) ($article['image_alt'] ?? '')) ?>">
    </div>
  </div>

  <div class="actions field-full">
        <button class="btn" type="submit">Enregistrer</button>
    </div>
</form>

<script>
const titleInput = document.getElementById('title');
const slugInput = document.getElementById('slug');
const articleForm = document.querySelector('form[action="<?= e($action) ?>"]');
const csrfInput = document.querySelector('input[name="_csrf"]');
const contentTextarea = document.getElementById('content');

let tinyEditorReady = false;
let tinyEditorLoadingPromise = null;

function loadTinyMceScript() {
  if (window.tinymce) {
    return Promise.resolve(window.tinymce);
  }

  if (tinyEditorLoadingPromise) {
    return tinyEditorLoadingPromise;
  }

  tinyEditorLoadingPromise = new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = '/public/vendor/tinymce/tinymce.min.js';
    script.async = true;
    script.onload = () => resolve(window.tinymce);
    script.onerror = () => reject(new Error('Impossible de charger TinyMCE'));
    document.head.appendChild(script);
  });

  return tinyEditorLoadingPromise;
}

function initTinyMceIfNeeded() {
  if (tinyEditorReady || !contentTextarea) {
    return Promise.resolve();
  }

  return loadTinyMceScript().then((tiny) => {
    if (!tiny || tinyEditorReady) {
      return;
    }

    return tiny.init({
      selector: '#content',
      height: 420,
      menubar: false,
      branding: false,
      promotion: false,
      plugins: 'lists link image table autoresize wordcount searchreplace',
      toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link image table | removeformat',
      block_formats: 'Paragraphe=p; Titre section=h2; Sous-section=h3',
      content_style: "body { font-family: Inter, Arial, sans-serif; font-size: 15px; line-height: 1.6; }",
      paste_as_text: true,
      images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());

        fetch('/articles/editor-upload', {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-Token': csrfInput?.value || ''
          },
          credentials: 'same-origin'
        })
          .then(async (response) => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.location) {
              throw new Error(data.error || 'Échec upload image');
            }
            resolve(data.location);
          })
          .catch((error) => reject(error.message || 'Échec upload image'));
      }),
      setup: (editor) => {
        editor.on('init', () => {
          tinyEditorReady = true;
        });
      }
    });
  }).catch(() => {
    // Fallback: textarea native si TinyMCE ne charge pas
  });
}

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

if (contentTextarea) {
  contentTextarea.addEventListener('focus', initTinyMceIfNeeded, { once: true });
  contentTextarea.addEventListener('click', initTinyMceIfNeeded, { once: true });
}

if (articleForm) {
  articleForm.addEventListener('submit', (event) => {
    if (window.tinymce && tinyEditorReady) {
      window.tinymce.triggerSave();
      const editor = window.tinymce.get('content');
      const plainText = editor ? editor.getContent({ format: 'text' }).trim() : '';
      if (!plainText) {
        event.preventDefault();
        alert('Le contenu est obligatoire.');
        editor?.focus();
      }
      return;
    }

    const plainText = (contentTextarea?.value || '').replace(/\s+/g, ' ').trim();
    if (!plainText) {
      event.preventDefault();
      alert('Le contenu est obligatoire.');
      contentTextarea?.focus();
    }
  });
}
</script>
