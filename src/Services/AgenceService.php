<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AgenceRepository;
use InvalidArgumentException;

/**
 * Logique métier des agences (gérées uniquement par l'administrateur).
 */
final class AgenceService
{
    public function __construct(private readonly AgenceRepository $agences)
    {
    }

    /**
     * Valide le nom d'une agence.
     *
     * @param array<string, mixed> $data
     * @param int|null             $exceptId agence à exclure du contrôle d'unicité (modification)
     *
     * @return list<string> messages d'erreur (vide si valide)
     */
    public function validate(array $data, ?int $exceptId = null): array
    {
        $errors = [];
        $nom = trim((string) ($data['nom'] ?? ''));

        if ($nom === '') {
            $errors[] = "Le nom de l'agence est obligatoire.";
        } elseif (mb_strlen($nom) > 100) {
            $errors[] = "Le nom de l'agence ne doit pas dépasser 100 caractères.";
        } elseif ($this->agences->existsByNom($nom, $exceptId)) {
            $errors[] = "Une agence portant ce nom existe déjà.";
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws InvalidArgumentException
     */
    public function create(array $data): int
    {
        $this->assertValid($data, null);

        return $this->agences->create(trim((string) ($data['nom'] ?? '')));
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws InvalidArgumentException
     */
    public function update(int $id, array $data): void
    {
        $this->assertValid($data, $id);
        $this->agences->update($id, trim((string) ($data['nom'] ?? '')));
    }

    /**
     * Supprime une agence si elle n'est rattachée à aucun trajet.
     *
     * @return bool true si supprimée, false si des trajets l'utilisent
     */
    public function delete(int $id): bool
    {
        if ($this->agences->countTrajets($id) > 0) {
            return false;
        }

        $this->agences->delete($id);

        return true;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws InvalidArgumentException
     */
    private function assertValid(array $data, ?int $exceptId): void
    {
        $errors = $this->validate($data, $exceptId);
        if ($errors !== []) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }
    }
}
