<?php
/** @var \App\Core\View $this */
/** @var int $nbUtilisateurs */
/** @var int $nbAgences */
/** @var int $nbTrajets */
$cards = [
    ['Utilisateurs', $nbUtilisateurs, '/admin/utilisateurs'],
    ['Agences', $nbAgences, '/admin/agences'],
    ['Trajets', $nbTrajets, '/admin/trajets'],
];
?>
<h1 class="h3 mb-4">Tableau de bord</h1>
<div class="row g-3">
    <?php foreach ($cards as [$libelle, $nombre, $lien]): ?>
        <div class="col-md-4">
            <a href="<?= $lien ?>" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h2 class="h5 text-dark"><?= $this->e($libelle) ?></h2>
                        <p class="display-5 mb-0 text-primary"><?= $this->e($nombre) ?></p>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
