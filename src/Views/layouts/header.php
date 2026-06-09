<?php
/** @var \App\Core\View $this */
/** @var string $appName */
/** @var \App\Models\Utilisateur|null $currentUser */
?>
<header class="app-header d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-2">
    <a href="/" class="fw-bold text-dark text-decoration-none fs-5"><?= $this->e($appName) ?></a>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <?php if ($currentUser === null): ?>
            <a href="/login" class="btn btn-dark">Connexion</a>
        <?php elseif ($currentUser->estAdmin()): ?>
            <a href="/admin/utilisateurs" class="btn btn-light border">Utilisateurs</a>
            <a href="/admin/agences" class="btn btn-light border">Agences</a>
            <a href="/admin/trajets" class="btn btn-light border">Trajets</a>
            <span class="ms-2">Bonjour <?= $this->e($currentUser->nomComplet()) ?></span>
            <a href="/logout" class="btn btn-dark">Déconnexion</a>
        <?php else: ?>
            <a href="/trajets/creer" class="btn btn-dark">Créer un trajet</a>
            <span class="ms-2">Bonjour <?= $this->e($currentUser->nomComplet()) ?></span>
            <a href="/logout" class="btn btn-dark">Déconnexion</a>
        <?php endif; ?>
    </div>
</header>
