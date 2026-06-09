<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Utilisateur;
use App\Repositories\UtilisateurRepository;

/**
 * Gère l'état d'authentification de l'utilisateur courant via la session.
 *
 * Seul l'identifiant est conservé en session ; l'objet Utilisateur est
 * rechargé depuis la base à la demande (et mis en cache pour la requête).
 */
final class Auth
{
    private static ?Utilisateur $cached = null;

    /** Ouvre une session authentifiée pour l'utilisateur donné. */
    public static function login(Utilisateur $utilisateur): void
    {
        Session::regenerate();
        Session::set('user_id', $utilisateur->id);
        self::$cached = $utilisateur;
    }

    /** Ferme la session authentifiée. */
    public static function logout(): void
    {
        Session::remove('user_id');
        self::$cached = null;
    }

    /** Identifiant de l'utilisateur connecté, ou null. */
    public static function id(): ?int
    {
        $id = Session::get('user_id');

        return is_int($id) ? $id : null;
    }

    /** Utilisateur connecté (rechargé depuis la base), ou null. */
    public static function user(): ?Utilisateur
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $id = self::id();
        if ($id === null) {
            return null;
        }

        $repo = new UtilisateurRepository(Database::getInstance());

        return self::$cached = $repo->findById($id);
    }

    /** Indique si un utilisateur est connecté. */
    public static function check(): bool
    {
        return self::id() !== null;
    }

    /** Indique si l'utilisateur connecté est administrateur. */
    public static function isAdmin(): bool
    {
        $user = self::user();

        return $user !== null && $user->estAdmin();
    }

    /** Réinitialise le cache (utile pour les tests). */
    public static function reset(): void
    {
        self::$cached = null;
    }
}
