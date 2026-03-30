<section class="section-header">
    <h1>Catégories</h1>
    <a class="btn" href="/categories/create">Créer une catégorie</a>
</section>

<section class="card">
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Slug</th>
                <th>Créée le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= e((string) $category['name']) ?></td>
                <td><?= e((string) $category['slug']) ?></td>
                <td><?= e((string) $category['created_at']) ?></td>
                <td class="cell-actions">
                    <div class="action-group">
                    <a class="btn btn-small" href="/categories/edit/<?= (int) $category['id'] ?>">Modifier</a>
                    <form method="post" action="/categories/delete/<?= (int) $category['id'] ?>" class="inline-form" onsubmit="return confirm('Confirmer la suppression ?')">
                        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                        <button class="btn btn-danger btn-small" type="submit">Supprimer</button>
                    </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
