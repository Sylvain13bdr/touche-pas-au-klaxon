<?php
/** @var \App\Core\View $this */
/** @var string|null $email */
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-4">Connexion</h1>
                <form method="post" action="/login" novalidate>
                    <div class="mb-3">
                        <label class="form-label" for="email">Adresse email</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= $this->e($email ?? '') ?>" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</div>
