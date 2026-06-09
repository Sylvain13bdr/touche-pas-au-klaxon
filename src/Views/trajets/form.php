<?php
/** @var \App\Core\View $this */
/** @var list<\App\Models\Agence> $agences */
/** @var \App\Models\Trajet|null $trajet */
/** @var list<string> $errors */
/** @var array<string, mixed> $old */
/** @var \App\Models\Utilisateur|null $currentUser */

$isEdit = $trajet !== null;
$action = $isEdit ? '/trajets/modifier' : '/trajets/creer';

// Valeur courante d'un champ : priorité aux données soumises (repeuplage
// après erreur), puis aux valeurs du trajet en édition, sinon vide.
$vDepart  = (string) ($old['agence_depart_id']  ?? ($trajet->agenceDepartId  ?? ''));
$vArrivee = (string) ($old['agence_arrivee_id'] ?? ($trajet->agenceArriveeId ?? ''));
$vDDepart = (string) ($old['date_heure_depart'] ?? ($trajet?->departForInput()  ?? ''));
$vDArrivee = (string) ($old['date_heure_arrivee'] ?? ($trajet?->arriveeForInput() ?? ''));
$vTotal   = (string) ($old['places_totales']     ?? ($trajet->placesTotales     ?? ''));
$vDispo   = (string) ($old['places_disponibles'] ?? ($trajet->placesDisponibles ?? ''));
?>
<h1 class="h3 mb-4"><?= $isEdit ? 'Modifier un trajet' : 'Créer un trajet' ?></h1>

<?php if ($errors !== []): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= $this->e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= $action ?>" class="card shadow-sm" novalidate>
    <div class="card-body">
        <?php if ($trajet !== null): ?>
            <input type="hidden" name="id" value="<?= $this->e($trajet->id) ?>">
        <?php endif; ?>

        <fieldset class="mb-4" disabled>
            <legend class="h6 text-muted">Personne à contacter (vous)</legend>
            <div class="row g-2">
                <div class="col-md-4">
                    <input class="form-control" value="<?= $this->e($currentUser?->nomComplet()) ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?= $this->e($currentUser?->email) ?>">
                </div>
                <div class="col-md-4">
                    <input class="form-control" value="<?= $this->e($currentUser?->telephone) ?>">
                </div>
            </div>
        </fieldset>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="agence_depart_id">Agence de départ</label>
                <select class="form-select" id="agence_depart_id" name="agence_depart_id" required>
                    <option value="">— Choisir —</option>
                    <?php foreach ($agences as $agence): ?>
                        <option value="<?= $this->e($agence->id) ?>" <?= $vDepart === (string) $agence->id ? 'selected' : '' ?>>
                            <?= $this->e($agence->nom) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="agence_arrivee_id">Agence d'arrivée</label>
                <select class="form-select" id="agence_arrivee_id" name="agence_arrivee_id" required>
                    <option value="">— Choisir —</option>
                    <?php foreach ($agences as $agence): ?>
                        <option value="<?= $this->e($agence->id) ?>" <?= $vArrivee === (string) $agence->id ? 'selected' : '' ?>>
                            <?= $this->e($agence->nom) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="date_heure_depart">Départ (date et heure)</label>
                <input type="datetime-local" class="form-control" id="date_heure_depart"
                       name="date_heure_depart" value="<?= $this->e($vDDepart) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="date_heure_arrivee">Arrivée (date et heure)</label>
                <input type="datetime-local" class="form-control" id="date_heure_arrivee"
                       name="date_heure_arrivee" value="<?= $this->e($vDArrivee) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="places_totales">Nombre total de places</label>
                <input type="number" min="1" class="form-control" id="places_totales"
                       name="places_totales" value="<?= $this->e($vTotal) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="places_disponibles">Places disponibles</label>
                <input type="number" min="0" class="form-control" id="places_disponibles"
                       name="places_disponibles" value="<?= $this->e($vDispo) ?>" required>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Enregistrer les modifications' : 'Créer le trajet' ?></button>
            <a href="/" class="btn btn-light border">Annuler</a>
        </div>
    </div>
</form>
