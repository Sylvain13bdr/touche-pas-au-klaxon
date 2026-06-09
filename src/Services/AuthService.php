<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Utilisateur;
use App\Repositories\UtilisateurRepository;

/**
 * Service d'authentification : vérification des identifiants.
 */
final class AuthService
{
    public function __construct(private readonly UtilisateurRepository $utilisateurs)
    {
    }

    /**
     * Tente d'authentifier un utilisateur par email + mot de passe.
     *
     * @return Utilisateur|null l'utilisateur si les identifiants sont valides, sinon null.
     */
    public function attempt(string $email, string $motDePasse): ?Utilisateur
    {
        $utilisateur = $this->utilisateurs->findByEmail($email);

        if ($utilisateur === null) {
            return null;
        }

        if (!password_verify($motDePasse, $utilisateur->motDePasse)) {
            return null;
        }

        return $utilisateur;
    }
}
