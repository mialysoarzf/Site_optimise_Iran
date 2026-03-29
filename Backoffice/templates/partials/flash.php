<?php foreach (($flash['success'] ?? []) as $message): ?>
    <div class="alert alert-success"><?= e($message) ?></div>
<?php endforeach; ?>

<?php foreach (($flash['error'] ?? []) as $message): ?>
    <div class="alert alert-error"><?= e($message) ?></div>
<?php endforeach; ?>
