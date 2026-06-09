<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Repositories\AgenceRepository;
use App\Services\AgenceService;
use PHPUnit\Framework\TestCase;

/**
 * Tests des règles de validation et des écritures du service Agence.
 */
final class AgenceServiceTest extends TestCase
{
    public function testNomVideEstRejete(): void
    {
        $repo = $this->createMock(AgenceRepository::class);
        $repo->method('existsByNom')->willReturn(false);
        $service = new AgenceService($repo);

        self::assertContains("Le nom de l'agence est obligatoire.", $service->validate(['nom' => '   ']));
    }

    public function testNomTropLongEstRejete(): void
    {
        $repo = $this->createMock(AgenceRepository::class);
        $repo->method('existsByNom')->willReturn(false);
        $service = new AgenceService($repo);

        self::assertNotEmpty($service->validate(['nom' => str_repeat('a', 101)]));
    }

    public function testNomDejaExistantEstRejete(): void
    {
        $repo = $this->createMock(AgenceRepository::class);
        $repo->method('existsByNom')->willReturn(true);
        $service = new AgenceService($repo);

        self::assertContains("Une agence portant ce nom existe déjà.", $service->validate(['nom' => 'Paris']));
    }

    public function testCreationValideEcritLeNomNettoye(): void
    {
        $repo = $this->createMock(AgenceRepository::class);
        $repo->method('existsByNom')->willReturn(false);
        $repo->expects($this->once())->method('create')->with('Lyon')->willReturn(9);
        $service = new AgenceService($repo);

        self::assertSame(9, $service->create(['nom' => '  Lyon  ']));
    }

    public function testSuppressionRefuseeSiAgenceRattacheeADesTrajets(): void
    {
        $repo = $this->createMock(AgenceRepository::class);
        $repo->method('countTrajets')->willReturn(3);
        $repo->expects($this->never())->method('delete');
        $service = new AgenceService($repo);

        self::assertFalse($service->delete(1));
    }

    public function testSuppressionAutoriseeSiAgenceLibre(): void
    {
        $repo = $this->createMock(AgenceRepository::class);
        $repo->method('countTrajets')->willReturn(0);
        $repo->expects($this->once())->method('delete')->with(1);
        $service = new AgenceService($repo);

        self::assertTrue($service->delete(1));
    }
}
