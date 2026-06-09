<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Agence;
use App\Models\Trajet;
use App\Models\Utilisateur;
use PDO;

/**
 * Accès aux données de la table « trajet ».
 *
 * Les lectures effectuent les jointures vers les agences et l'auteur afin
 * d'hydrater des objets Trajet complets prêts pour l'affichage.
 */
class TrajetRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** Clause SELECT commune avec les jointures agences + auteur. */
    private function baseSelect(): string
    {
        return 'SELECT t.id, t.date_heure_depart, t.date_heure_arrivee,
                       t.places_totales, t.places_disponibles,
                       t.agence_depart_id, t.agence_arrivee_id, t.utilisateur_id,
                       ad.nom AS depart_nom,
                       aa.nom AS arrivee_nom,
                       u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                       u.email AS auteur_email, u.telephone AS auteur_telephone,
                       u.role AS auteur_role
                FROM trajet t
                JOIN agence ad ON ad.id = t.agence_depart_id
                JOIN agence aa ON aa.id = t.agence_arrivee_id
                JOIN utilisateur u ON u.id = t.utilisateur_id';
    }

    /**
     * Trajets à venir disposant encore de places, triés par date de départ
     * croissante (liste de la page d'accueil et de la vue connectée).
     *
     * @return list<Trajet>
     */
    public function findUpcomingWithSeats(): array
    {
        $sql = $this->baseSelect()
            . ' WHERE t.date_heure_depart > NOW() AND t.places_disponibles > 0'
            . ' ORDER BY t.date_heure_depart ASC';

        $stmt = $this->pdo->query($sql);

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt !== false ? $stmt->fetchAll() : [];

        return array_map(fn (array $row): Trajet => $this->hydrate($row), $rows);
    }

    /**
     * Tous les trajets (tableau de bord administrateur), du plus récent
     * départ au plus ancien.
     *
     * @return list<Trajet>
     */
    public function findAll(): array
    {
        $sql = $this->baseSelect() . ' ORDER BY t.date_heure_depart DESC';
        $stmt = $this->pdo->query($sql);

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt !== false ? $stmt->fetchAll() : [];

        return array_map(fn (array $row): Trajet => $this->hydrate($row), $rows);
    }

    /** Recherche un trajet par son identifiant (avec jointures). */
    public function findById(int $id): ?Trajet
    {
        $stmt = $this->pdo->prepare($this->baseSelect() . ' WHERE t.id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!is_array($row)) {
            return null;
        }

        /** @var array<string, mixed> $row */
        return $this->hydrate($row);
    }

    /** Crée un trajet et retourne son identifiant. */
    public function create(
        int $agenceDepartId,
        int $agenceArriveeId,
        string $dateHeureDepart,
        string $dateHeureArrivee,
        int $placesTotales,
        int $placesDisponibles,
        int $utilisateurId
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO trajet
                (agence_depart_id, agence_arrivee_id, date_heure_depart, date_heure_arrivee,
                 places_totales, places_disponibles, utilisateur_id)
             VALUES
                (:dep, :arr, :ddep, :darr, :ptot, :pdispo, :uid)'
        );
        $stmt->execute([
            'dep'    => $agenceDepartId,
            'arr'    => $agenceArriveeId,
            'ddep'   => $dateHeureDepart,
            'darr'   => $dateHeureArrivee,
            'ptot'   => $placesTotales,
            'pdispo' => $placesDisponibles,
            'uid'    => $utilisateurId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /** Met à jour un trajet existant. */
    public function update(
        int $id,
        int $agenceDepartId,
        int $agenceArriveeId,
        string $dateHeureDepart,
        string $dateHeureArrivee,
        int $placesTotales,
        int $placesDisponibles
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE trajet SET
                agence_depart_id   = :dep,
                agence_arrivee_id  = :arr,
                date_heure_depart  = :ddep,
                date_heure_arrivee = :darr,
                places_totales     = :ptot,
                places_disponibles = :pdispo
             WHERE id = :id'
        );
        $stmt->execute([
            'dep'    => $agenceDepartId,
            'arr'    => $agenceArriveeId,
            'ddep'   => $dateHeureDepart,
            'darr'   => $dateHeureArrivee,
            'ptot'   => $placesTotales,
            'pdispo' => $placesDisponibles,
            'id'     => $id,
        ]);
    }

    /** Supprime un trajet. */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM trajet WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Construit un objet Trajet (et ses objets liés) depuis une ligne jointe.
     *
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Trajet
    {
        $depart = new Agence((int) $row['agence_depart_id'], (string) $row['depart_nom']);
        $arrivee = new Agence((int) $row['agence_arrivee_id'], (string) $row['arrivee_nom']);
        $auteur = new Utilisateur(
            (int) $row['utilisateur_id'],
            (string) $row['auteur_nom'],
            (string) $row['auteur_prenom'],
            (string) $row['auteur_telephone'],
            (string) $row['auteur_email'],
            (string) $row['auteur_role'],
        );

        return new Trajet(
            (int) $row['id'],
            (string) $row['date_heure_depart'],
            (string) $row['date_heure_arrivee'],
            (int) $row['places_totales'],
            (int) $row['places_disponibles'],
            (int) $row['agence_depart_id'],
            (int) $row['agence_arrivee_id'],
            (int) $row['utilisateur_id'],
            $depart,
            $arrivee,
            $auteur,
        );
    }
}
