<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Une agence (ville) entre lesquelles s'effectuent les trajets.
 */
final class Agence
{
    public function __construct(
        public readonly int $id,
        public readonly string $nom,
    ) {
    }

    /**
     * Construit une agence à partir d'une ligne de résultat.
     *
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['nom'],
        );
    }
}
