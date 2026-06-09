<?php
/** @var \App\Core\View $this */
/** @var list<\App\Models\Trajet> $trajets */
?>
<h1 class="h3 mb-4">Trajets</h1>

<table class="table table-striped table-app align-middle">
    <thead>
        <tr>
            <th>Départ</th>
            <th>Date / heure</th>
            <th>Destination</th>
            <th>Date / heure</th>
            <th>Places</th>
            <th>Auteur</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($trajets === []): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Aucun trajet.</td></tr>
        <?php else: ?>
            <?php foreach ($trajets as $trajet): ?>
                <tr>
                    <td><?= $this->e($trajet->agenceDepart?->nom) ?></td>
                    <td><?= $this->e($trajet->dateDepart()) ?> <?= $this->e($trajet->heureDepart()) ?></td>
                    <td><?= $this->e($trajet->agenceArrivee?->nom) ?></td>
                    <td><?= $this->e($trajet->dateArrivee()) ?> <?= $this->e($trajet->heureArrivee()) ?></td>
                    <td><?= $this->e($trajet->placesDisponibles) ?> / <?= $this->e($trajet->placesTotales) ?></td>
                    <td><?= $this->e($trajet->auteur?->nomComplet()) ?></td>
                    <td class="text-end">
                        <form method="post" action="/admin/trajets/supprimer" class="d-inline"
                              onsubmit="return confirm('Supprimer ce trajet ?');">
                            <input type="hidden" name="id" value="<?= $this->e($trajet->id) ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
