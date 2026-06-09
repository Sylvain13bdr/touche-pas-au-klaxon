<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Encapsule la session PHP et la gestion des messages flash.
 *
 * Un message flash est stocké en session puis consommé (et supprimé) au
 * prochain affichage : il sert à confirmer une opération après une
 * redirection (cf. cahier des charges).
 */
final class Session
{
    /** Démarre la session si ce n'est pas déjà fait. */
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /** Enregistre une valeur en session. */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /** Récupère une valeur de session, ou $default si absente. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /** Indique si une clé existe en session. */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /** Supprime une valeur de session. */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /** Détruit complètement la session (déconnexion). */
    public static function destroy(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /** Régénère l'identifiant de session (à la connexion, anti-fixation). */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Ajoute un message flash.
     *
     * @param string $type 'success', 'danger', 'warning'…
     */
    public static function addFlash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Retourne tous les messages flash puis les supprime.
     *
     * @return list<array{type: string, message: string}>
     */
    public static function takeFlashes(): array
    {
        /** @var list<array{type: string, message: string}> $flashes */
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);

        return $flashes;
    }
}
