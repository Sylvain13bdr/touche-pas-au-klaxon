<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Utilisateur;
use PDO;

/**
 * Accès aux données de la table « utilisateur ».
 *
 * L'application ne crée, ne modifie ni ne supprime d'employés (données
 * issues du SI RH) : ce dépôt n'expose donc que des lectures.
 */
final class UtilisateurRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Recherche un utilisateur par son email (mot de passe inclus, pour
     * l'authentification).
     */
    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return null;
        }

        /** @var array<string, mixed> $row */
        return Utilisateur::fromRow($row);
    }

    /** Recherche un utilisateur par son identifiant. */
    public function findById(int $id): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return null;
        }

        /** @var array<string, mixed> $row */
        return Utilisateur::fromRow($row);
    }

    /**
     * Liste tous les utilisateurs (pour le tableau de bord administrateur).
     *
     * @return list<Utilisateur>
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, nom, prenom, telephone, email, role FROM utilisateur ORDER BY nom, prenom'
        );

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt !== false ? $stmt->fetchAll() : [];

        return array_map(static fn (array $row): Utilisateur => Utilisateur::fromRow($row), $rows);
    }
}
