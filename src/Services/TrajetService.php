<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AgenceRepository;
use App\Repositories\TrajetRepository;
use DateTimeImmutable;
use InvalidArgumentException;
use LogicException;

/**
 * Logique métier des trajets : validation des règles de cohérence puis
 * orchestration des écritures via les dépôts.
 */
final class TrajetService
{
    public function __construct(
        private readonly TrajetRepository $trajets,
        private readonly AgenceRepository $agences,
    ) {
    }

    /**
     * Valide les données d'un trajet issues d'un formulaire.
     *
     * @param array<string, mixed> $data
     *
     * @return list<string> liste des messages d'erreur (vide si valide)
     */
    public function validate(array $data): array
    {
        $errors = [];

        $depart = $this->toInt($data['agence_depart_id'] ?? null);
        $arrivee = $this->toInt($data['agence_arrivee_id'] ?? null);

        if ($depart === null || $this->agences->findById($depart) === null) {
            $errors[] = "L'agence de départ est invalide.";
        }
        if ($arrivee === null || $this->agences->findById($arrivee) === null) {
            $errors[] = "L'agence d'arrivée est invalide.";
        }
        if ($depart !== null && $arrivee !== null && $depart === $arrivee) {
            $errors[] = "L'agence de départ et l'agence d'arrivée doivent être différentes.";
        }

        $dateDepart = $this->parseDateTime($data['date_heure_depart'] ?? null);
        $dateArrivee = $this->parseDateTime($data['date_heure_arrivee'] ?? null);

        if ($dateDepart === null) {
            $errors[] = "La date et l'heure de départ sont invalides.";
        }
        if ($dateArrivee === null) {
            $errors[] = "La date et l'heure d'arrivée sont invalides.";
        }
        if ($dateDepart !== null && $dateArrivee !== null && $dateArrivee <= $dateDepart) {
            $errors[] = "L'heure d'arrivée doit être postérieure à l'heure de départ.";
        }
        if ($dateDepart !== null && $dateDepart < new DateTimeImmutable()) {
            $errors[] = "Le départ doit être planifié dans le futur.";
        }

        $placesTotales = $this->toInt($data['places_totales'] ?? null);
        $placesDispo = $this->toInt($data['places_disponibles'] ?? null);

        if ($placesTotales === null || $placesTotales < 1) {
            $errors[] = "Le nombre total de places doit être d'au moins 1.";
        }
        if ($placesDispo === null || $placesDispo < 0) {
            $errors[] = "Le nombre de places disponibles est invalide.";
        }
        if ($placesTotales !== null && $placesDispo !== null && $placesDispo > $placesTotales) {
            $errors[] = "Les places disponibles ne peuvent pas dépasser le nombre total de places.";
        }

        return $errors;
    }

    /**
     * Crée un trajet après validation.
     *
     * @param array<string, mixed> $data
     *
     * @throws InvalidArgumentException si les données sont invalides
     *
     * @return int identifiant du trajet créé
     */
    public function create(array $data, int $auteurId): int
    {
        $this->assertValid($data);
        [$dep, $arr, $ddep, $darr, $ptot, $pdispo] = $this->payload($data);

        return $this->trajets->create($dep, $arr, $ddep, $darr, $ptot, $pdispo, $auteurId);
    }

    /**
     * Met à jour un trajet après validation.
     *
     * @param array<string, mixed> $data
     *
     * @throws InvalidArgumentException si les données sont invalides
     */
    public function update(int $id, array $data): void
    {
        $this->assertValid($data);
        [$dep, $arr, $ddep, $darr, $ptot, $pdispo] = $this->payload($data);

        $this->trajets->update($id, $dep, $arr, $ddep, $darr, $ptot, $pdispo);
    }

    /** Supprime un trajet. */
    public function delete(int $id): void
    {
        $this->trajets->delete($id);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws InvalidArgumentException
     */
    private function assertValid(array $data): void
    {
        $errors = $this->validate($data);
        if ($errors !== []) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }
    }

    /**
     * Extrait les valeurs typées d'un jeu de données déjà validé.
     *
     * @param array<string, mixed> $data
     *
     * @return array{0:int,1:int,2:string,3:string,4:int,5:int}
     */
    private function payload(array $data): array
    {
        $dep = $this->toInt($data['agence_depart_id'] ?? null) ?? throw new LogicException('depart');
        $arr = $this->toInt($data['agence_arrivee_id'] ?? null) ?? throw new LogicException('arrivee');
        $ddep = $this->parseDateTime($data['date_heure_depart'] ?? null) ?? throw new LogicException('ddep');
        $darr = $this->parseDateTime($data['date_heure_arrivee'] ?? null) ?? throw new LogicException('darr');
        $ptot = $this->toInt($data['places_totales'] ?? null) ?? throw new LogicException('ptot');
        $pdispo = $this->toInt($data['places_disponibles'] ?? null) ?? throw new LogicException('pdispo');

        return [$dep, $arr, $ddep->format('Y-m-d H:i:s'), $darr->format('Y-m-d H:i:s'), $ptot, $pdispo];
    }

    /** Convertit une valeur de formulaire en entier positif, ou null. */
    private function toInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && $value !== '' && ctype_digit($value)) {
            return (int) $value;
        }

        return null;
    }

    /** Parse une date/heure (format datetime-local ou SQL), ou null. */
    private function parseDateTime(mixed $value): ?DateTimeImmutable
    {
        if (!is_string($value) || $value === '') {
            return null;
        }

        $value = str_replace('T', ' ', $value);

        foreach (['Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
            $date = DateTimeImmutable::createFromFormat($format, $value);
            if ($date !== false) {
                return $date;
            }
        }

        return null;
    }
}
