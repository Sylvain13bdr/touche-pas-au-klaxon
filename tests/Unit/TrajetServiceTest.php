<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Models\Agence;
use App\Repositories\AgenceRepository;
use App\Repositories\TrajetRepository;
use App\Services\TrajetService;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Tests des règles de cohérence et des écritures du service Trajet.
 */
final class TrajetServiceTest extends TestCase
{
    /** Dépôt d'agences simulé : seules les agences 1, 2 et 3 existent. */
    private function agencesMock(): AgenceRepository
    {
        $mock = $this->createMock(AgenceRepository::class);
        $mock->method('findById')->willReturnCallback(
            static fn (int $id): ?Agence => in_array($id, [1, 2, 3], true) ? new Agence($id, 'Ville ' . $id) : null
        );

        return $mock;
    }

    /** Jeu de données valide réutilisable. */
    private function donneesValides(): array
    {
        return [
            'agence_depart_id'   => '1',
            'agence_arrivee_id'  => '2',
            'date_heure_depart'  => (new DateTimeImmutable('+2 days'))->format('Y-m-d\TH:i'),
            'date_heure_arrivee' => (new DateTimeImmutable('+2 days +3 hours'))->format('Y-m-d\TH:i'),
            'places_totales'     => '4',
            'places_disponibles' => '3',
        ];
    }

    public function testDonneesValidesNeProduisentAucuneErreur(): void
    {
        $service = new TrajetService($this->createMock(TrajetRepository::class), $this->agencesMock());

        self::assertSame([], $service->validate($this->donneesValides()));
    }

    public function testAgencesIdentiquesSontRejetees(): void
    {
        $service = new TrajetService($this->createMock(TrajetRepository::class), $this->agencesMock());
        $data = $this->donneesValides();
        $data['agence_arrivee_id'] = '1';

        self::assertContains(
            "L'agence de départ et l'agence d'arrivée doivent être différentes.",
            $service->validate($data)
        );
    }

    public function testAgenceInexistanteEstRejetee(): void
    {
        $service = new TrajetService($this->createMock(TrajetRepository::class), $this->agencesMock());
        $data = $this->donneesValides();
        $data['agence_arrivee_id'] = '99';

        self::assertContains("L'agence d'arrivée est invalide.", $service->validate($data));
    }

    public function testArriveeAvantDepartEstRejetee(): void
    {
        $service = new TrajetService($this->createMock(TrajetRepository::class), $this->agencesMock());
        $data = $this->donneesValides();
        $data['date_heure_arrivee'] = (new DateTimeImmutable('+2 days -1 hour'))->format('Y-m-d\TH:i');

        self::assertContains(
            "L'heure d'arrivée doit être postérieure à l'heure de départ.",
            $service->validate($data)
        );
    }

    public function testDepartDansLePasseEstRejete(): void
    {
        $service = new TrajetService($this->createMock(TrajetRepository::class), $this->agencesMock());
        $data = $this->donneesValides();
        $data['date_heure_depart'] = (new DateTimeImmutable('-1 day'))->format('Y-m-d\TH:i');
        $data['date_heure_arrivee'] = (new DateTimeImmutable('-1 day +2 hours'))->format('Y-m-d\TH:i');

        self::assertContains('Le départ doit être planifié dans le futur.', $service->validate($data));
    }

    public function testPlacesDisponiblesSuperieuresAuTotalRejetees(): void
    {
        $service = new TrajetService($this->createMock(TrajetRepository::class), $this->agencesMock());
        $data = $this->donneesValides();
        $data['places_totales'] = '2';
        $data['places_disponibles'] = '5';

        self::assertContains(
            'Les places disponibles ne peuvent pas dépasser le nombre total de places.',
            $service->validate($data)
        );
    }

    public function testCreationValideEcritEtRetourneLIdentifiant(): void
    {
        $trajets = $this->createMock(TrajetRepository::class);
        $trajets->expects($this->once())
            ->method('create')
            ->with(1, 2, self::isType('string'), self::isType('string'), 4, 3, 7)
            ->willReturn(42);

        $service = new TrajetService($trajets, $this->agencesMock());

        self::assertSame(42, $service->create($this->donneesValides(), 7));
    }

    public function testCreationInvalideLeveUneExceptionEtNEcritRien(): void
    {
        $trajets = $this->createMock(TrajetRepository::class);
        $trajets->expects($this->never())->method('create');

        $service = new TrajetService($trajets, $this->agencesMock());
        $data = $this->donneesValides();
        $data['agence_arrivee_id'] = '1'; // identique au départ

        $this->expectException(InvalidArgumentException::class);
        $service->create($data, 7);
    }

    public function testSuppressionDelegueAuDepot(): void
    {
        $trajets = $this->createMock(TrajetRepository::class);
        $trajets->expects($this->once())->method('delete')->with(15);

        $service = new TrajetService($trajets, $this->agencesMock());
        $service->delete(15);
    }
}
