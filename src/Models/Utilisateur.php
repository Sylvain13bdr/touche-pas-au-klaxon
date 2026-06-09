<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Un utilisateur de l'application : employé (importé du SI RH) ou
 * administrateur. Le mot de passe stocké est toujours haché.
 */
final class Utilisateur
{
    public const ROLE_EMPLOYE = 'employe';
    public const ROLE_ADMIN   = 'admin';

    public function __construct(
        public readonly int $id,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $telephone,
        public readonly string $email,
        public readonly string $role,
        public readonly string $motDePasse = '',
    ) {
    }

    /** Indique si l'utilisateur possède le rôle administrateur. */
    public function estAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /** Retourne « Prénom Nom » pour l'affichage. */
    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    /**
     * Construit un utilisateur à partir d'une ligne de résultat.
     *
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['nom'],
            (string) $row['prenom'],
            (string) $row['telephone'],
            (string) $row['email'],
            (string) $row['role'],
            isset($row['mot_de_passe']) ? (string) $row['mot_de_passe'] : '',
        );
    }
}
