<?php
/** @var \App\Core\View $this */
/** @var list<\App\Models\Agence> $agences */
?>
<h1 class="h3 mb-4">Agences</h1>

<form method="post" action="/admin/agences/creer" class="row g-2 mb-4">
    <div class="col-auto">
        <input type="text" name="nom" class="form-control" placeholder="Nom de la nouvelle agence" maxlength="100" required>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </div>
</form>

<table class="table table-striped table-app align-middle">
    <thead>
        <tr>
            <th>Agence</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($agences as $agence): ?>
            <tr>
                <td>
                    <form method="post" action="/admin/agences/modifier" class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="id" value="<?= $this->e($agence->id) ?>">
                        <input type="text" name="nom" value="<?= $this->e($agence->nom) ?>"
                               class="form-control form-control-sm" style="max-width: 320px" maxlength="100" required>
                        <button type="submit" class="btn btn-sm btn-warning">Modifier</button>
                    </form>
                </td>
                <td class="text-end">
                    <form method="post" action="/admin/agences/supprimer" class="d-inline"
                          onsubmit="return confirm('Supprimer cette agence ?');">
                        <input type="hidden" name="id" value="<?= $this->e($agence->id) ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
