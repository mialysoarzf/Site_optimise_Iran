<section>
    <h1>Dashboard</h1>
    <div class="grid-4">
        <article class="card stat"><h2><?= e((string) $counters['total_articles']) ?></h2><p>Total articles</p></article>
        <article class="card stat"><h2><?= e((string) $counters['drafts']) ?></h2><p>Brouillons</p></article>
        <article class="card stat"><h2><?= e((string) $counters['published']) ?></h2><p>Publiés</p></article>
        <article class="card stat"><h2><?= e((string) $counters['categories']) ?></h2><p>Catégories</p></article>
    </div>
</section>

<section class="card">
    <h2>Derniers articles modifiés</h2>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Modifié</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($latestArticles as $item): ?>
            <tr>
                <td><a href="/admin/articles/edit/<?= (int) $item['id'] ?>"><?= e((string) $item['title']) ?></a></td>
                <td><?= e((string) ($item['category_name'] ?? '—')) ?></td>
                <td><?= e((string) $item['status']) ?></td>
                <td><?= e((string) $item['updated_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
