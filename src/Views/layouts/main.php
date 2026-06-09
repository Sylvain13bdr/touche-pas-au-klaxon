<?php
/** @var \App\Core\View $this */
/** @var string $appName */
/** @var \App\Models\Utilisateur|null $currentUser */
/** @var list<array{type: string, message: string}> $flashes */
/** @var string $content */
$pageTitle = isset($title) && is_string($title) ? $title : $appName;
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->e($pageTitle) ?> — <?= $this->e($appName) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="container py-4">
        <?= $this->renderPartial('layouts/header', ['appName' => $appName, 'currentUser' => $currentUser]) ?>

        <?php foreach ($flashes as $flash): ?>
            <div class="alert alert-<?= $this->e($flash['type']) ?> mt-3 mb-0" role="alert">
                <?= $this->e($flash['message']) ?>
            </div>
        <?php endforeach; ?>

        <main class="mt-4">
            <?= $content ?>
        </main>
    </div>

    <?= $this->renderPartial('layouts/footer', []) ?>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
