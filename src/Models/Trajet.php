<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

/**
 * Un trajet de covoiturage proposé par un utilisateur entre deux agences.
 *
 * Les objets liés (agences de départ/arrivée, auteur) sont optionnels :
 * ils ne sont hydratés que lorsque la requête effectue les jointures.
 */
final class Trajet
{
    public function __construct(
        public readonly int $id,
        public readonly string $dateHeureDepart,
        public readonly string $dateHeureArrivee,
        public readonly int $placesTotales,
        public readonly int $placesDisponibles,
        public readonly int $agenceDepartId,
        public readonly int $agenceArriveeId,
        public readonly int $utilisateurId,
        public readonly ?Agence $agenceDepart = null,
        public readonly ?Agence $agenceArrivee = null,
        public readonly ?Utilisateur $auteur = null,
    ) {
    }

    /** Le trajet est complet (plus aucune place disponible). */
    public function estComplet(): bool
    {
        return $this->placesDisponibles <= 0;
    }

    /** Indique si l'utilisateur donné est l'auteur du trajet. */
    public function estAuteur(int $utilisateurId): bool
    {
        return $this->utilisateurId === $utilisateurId;
    }

    /** Date de départ formatée (jj/mm/aa). */
    public function dateDepart(): string
    {
        return $this->format($this->dateHeureDepart, 'd/m/y');
    }

    /** Heure de départ formatée (HH:MM). */
    public function heureDepart(): string
    {
        return $this->format($this->dateHeureDepart, 'H:i');
    }

    /** Date d'arrivée formatée (jj/mm/aa). */
    public function dateArrivee(): string
    {
        return $this->format($this->dateHeureArrivee, 'd/m/y');
    }

    /** Heure d'arrivée formatée (HH:MM). */
    public function heureArrivee(): string
    {
        return $this->format($this->dateHeureArrivee, 'H:i');
    }

    /** Valeur du départ pour un champ <input type="datetime-local">. */
    public function departForInput(): string
    {
        return $this->format($this->dateHeureDepart, 'Y-m-d\TH:i');
    }

    /** Valeur de l'arrivée pour un champ <input type="datetime-local">. */
    public function arriveeForInput(): string
    {
        return $this->format($this->dateHeureArrivee, 'Y-m-d\TH:i');
    }

    /** Formate une date SQL ('Y-m-d H:i:s') selon le format demandé. */
    private function format(string $sqlDateTime, string $format): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $sqlDateTime);

        return $date !== false ? $date->format($format) : $sqlDateTime;
    }
}
