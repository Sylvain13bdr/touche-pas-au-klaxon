<?php
/** @var \App\Core\View $this */
/** @var list<\App\Models\Utilisateur> $utilisateurs */
?>
<h1 class="h3 mb-4">Utilisateurs</h1>

<table class="table table-striped table-app align-middle">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Rôle</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($utilisateurs as $utilisateur): ?>
            <tr>
                <td><?= $this->e($utilisateur->nom) ?></td>
                <td><?= $this->e($utilisateur->prenom) ?></td>
                <td><?= $this->e($utilisateur->email) ?></td>
                <td><?= $this->e($utilisateur->telephone) ?></td>
                <td>
                    <span class="badge bg-<?= $utilisateur->estAdmin() ? 'dark' : 'secondary' ?>">
                        <?= $this->e($utilisateur->role) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
