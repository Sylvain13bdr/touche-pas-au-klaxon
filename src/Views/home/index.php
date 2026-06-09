<?php
/** @var \App\Core\View $this */
/** @var list<\App\Models\Trajet> $trajets */
/** @var \App\Models\Utilisateur|null $currentUser */

$connecte = $currentUser !== null;
?>
<?php if ($connecte): ?>
    <h1 class="h3 mb-4">Trajets proposés</h1>
<?php else: ?>
    <h1 class="h3 mb-4">Pour obtenir plus d'informations sur un trajet, veuillez vous connecter</h1>
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
            <?php if ($connecte): ?><th class="text-end">Actions</th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if ($trajets === []): ?>
            <tr>
                <td colspan="<?= $connecte ? 8 : 7 ?>" class="text-center text-muted py-4">
                    Aucun trajet disponible pour le moment.
                </td>
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
                    <?php if ($currentUser !== null): ?>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-link p-0 me-2 text-secondary" title="Détails"
                                    data-bs-toggle="modal" data-bs-target="#detailsModal"
                                    data-auteur="<?= $this->e($trajet->auteur?->nomComplet()) ?>"
                                    data-tel="<?= $this->e($trajet->auteur?->telephone) ?>"
                                    data-email="<?= $this->e($trajet->auteur?->email) ?>"
                                    data-total="<?= $this->e($trajet->placesTotales) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/></svg>
                            </button>
                            <?php if ($trajet->estAuteur($currentUser->id)): ?>
                                <a href="/trajets/modifier?id=<?= $this->e($trajet->id) ?>" class="me-2 text-warning" title="Modifier">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>
                                </a>
                                <form method="post" action="/trajets/supprimer" class="d-inline"
                                      onsubmit="return confirm('Supprimer ce trajet ?');">
                                    <input type="hidden" name="id" value="<?= $this->e($trajet->id) ?>">
                                    <button type="submit" class="btn btn-link p-0 text-danger" title="Supprimer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if ($connecte): ?>
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="h5 modal-title">Détails du trajet</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2"><strong>Auteur :</strong> <span data-field="auteur"></span></p>
                    <p class="mb-2"><strong>Téléphone :</strong> <span data-field="tel"></span></p>
                    <p class="mb-2"><strong>Email :</strong> <span data-field="email"></span></p>
                    <p class="mb-0"><strong>Nombre total de places :</strong> <span data-field="total"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('detailsModal');
            if (!modal) { return; }
            modal.addEventListener('show.bs.modal', function (event) {
                var bouton = event.relatedTarget;
                if (!bouton) { return; }
                modal.querySelector('[data-field=auteur]').textContent = bouton.getAttribute('data-auteur') || '';
                modal.querySelector('[data-field=tel]').textContent = bouton.getAttribute('data-tel') || '';
                modal.querySelector('[data-field=email]').textContent = bouton.getAttribute('data-email') || '';
                modal.querySelector('[data-field=total]').textContent = bouton.getAttribute('data-total') || '';
            });
        });
    </script>
<?php endif; ?>
