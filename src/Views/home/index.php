<?php
/** @var \App\Core\View $this */
/** @var list<\App\Models\Trajet> $trajets */
/** @var \App\Models\Utilisateur|null $currentUser */
?>
<?php if ($currentUser === null): ?>
    <h1 class="h3 mb-4">Pour obtenir plus d'informations sur un trajet, veuillez vous connecter</h1>
<?php else: ?>
    <h1 class="h3 mb-4">Trajets proposés</h1>
<?php endif; ?>

<table class="table table-striped table-app align-middle">
    <thead>
        <tr>
            <th>Départ</th>
            <th>Date</th>
            <th>Heure</th>
            <th>Destination</th>
            <th>Date</th>
            <th>Heure</th>
            <th>Places</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($trajets === []): ?>
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Aucun trajet disponible pour le moment.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($trajets as $trajet): ?>
                <tr>
                    <td><?= $this->e($trajet->agenceDepart?->nom) ?></td>
                    <td><?= $this->e($trajet->dateDepart()) ?></td>
                    <td><?= $this->e($trajet->heureDepart()) ?></td>
                    <td><?= $this->e($trajet->agenceArrivee?->nom) ?></td>
                    <td><?= $this->e($trajet->dateArrivee()) ?></td>
                    <td><?= $this->e($trajet->heureArrivee()) ?></td>
                    <td><?= $this->e($trajet->placesDisponibles) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
