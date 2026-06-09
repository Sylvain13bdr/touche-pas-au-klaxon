<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Agence;
use PDO;

/**
 * Accès aux données de la table « agence ».
 */
class AgenceRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Liste toutes les agences, triées par nom.
     *
     * @return list<Agence>
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT id, nom FROM agence ORDER BY nom');

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt !== false ? $stmt->fetchAll() : [];

        return array_map(static fn (array $row): Agence => Agence::fromRow($row), $rows);
    }

    /** Recherche une agence par son identifiant. */
    public function findById(int $id): ?Agence
    {
        $stmt = $this->pdo->prepare('SELECT id, nom FROM agence WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return null;
        }

        /** @var array<string, mixed> $row */
        return Agence::fromRow($row);
    }

    /**
     * Indique si une agence portant ce nom existe déjà.
     * $exceptId permet d'exclure l'agence en cours de modification.
     */
    public function existsByNom(string $nom, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM agence WHERE nom = :nom';
        $params = ['nom' => $nom];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    /** Crée une agence et retourne son identifiant. */
    public function create(string $nom): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO agence (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $nom]);

        return (int) $this->pdo->lastInsertId();
    }

    /** Met à jour le nom d'une agence. */
    public function update(int $id, string $nom): void
    {
        $stmt = $this->pdo->prepare('UPDATE agence SET nom = :nom WHERE id = :id');
        $stmt->execute(['nom' => $nom, 'id' => $id]);
    }

    /** Supprime une agence (échoue si des trajets y sont rattachés). */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM agence WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** Compte les trajets rattachés à une agence (départ ou arrivée). */
    public function countTrajets(int $id): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM trajet WHERE agence_depart_id = :dep OR agence_arrivee_id = :arr'
        );
        $stmt->execute(['dep' => $id, 'arr' => $id]);

        return (int) $stmt->fetchColumn();
    }
}
