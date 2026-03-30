<section class="section-header">
    <h1>Articles</h1>
    <a class="btn" href="/articles/create">Créer un article</a>
</section>

<section class="card">
    <form method="get" action="/articles" class="search-row">
        <label for="q" class="sr-only">Recherche</label>
        <input id="q" type="search" name="q" placeholder="Rechercher par titre" value="<?= e($search) ?>">
        <button class="btn" type="submit">Rechercher</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Slug</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Modifié</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pagination['items'] as $item): ?>
            <tr>
                <td><?= e((string) $item['title']) ?></td>
                <td><?= e((string) $item['slug']) ?></td>
                <td><?= e((string) ($item['category_name'] ?? '—')) ?></td>
                <td>
                    <span class="badge <?= $item['status'] === 'published' ? 'badge-success' : 'badge-muted' ?>">
                        <?= e((string) $item['status']) ?>
                    </span>
                </td>
                <td><?= e((string) $item['updated_at']) ?></td>
                <td class="cell-actions">
                    <div class="action-group">
                    <a class="btn btn-small" href="/articles/edit/<?= (int) $item['id'] ?>">Modifier</a>
                    <form method="post" action="/articles/delete/<?= (int) $item['id'] ?>" class="inline-form" onsubmit="return confirm('Confirmer la suppression ?')">
                        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                        <button class="btn btn-danger btn-small" type="submit">Supprimer</button>
                    </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ((int) $pagination['pages'] > 1): ?>
        <nav class="pagination" aria-label="Pagination articles">
            <?php for ($p = 1; $p <= (int) $pagination['pages']; $p++): ?>
                <a class="<?= $p === (int) $pagination['page'] ? 'active' : '' ?>"
                   href="/articles?page=<?= $p ?>&q=<?= urlencode($search) ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</section>
