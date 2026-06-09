<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Point d'accès unique (singleton) à la connexion PDO.
 *
 * La connexion est créée à la demande puis réutilisée. Les requêtes
 * préparées et le mode exception protègent contre les injections SQL et
 * garantissent une gestion d'erreurs explicite.
 */
final class Database
{
    /** Instance PDO partagée. */
    private static ?PDO $instance = null;

    /** Classe utilitaire : pas d'instanciation. */
    private function __construct()
    {
    }

    /**
     * Retourne la connexion PDO, en la créant au premier appel.
     *
     * @throws RuntimeException si la connexion échoue.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            /** @var array{db: array{host:string, port:string, name:string, user:string, pass:string, charset:string}} $config */
            $config = require dirname(__DIR__, 2) . '/config/config.php';
            $db = $config['db'];

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $db['host'],
                $db['port'],
                $db['name'],
                $db['charset']
            );

            try {
                self::$instance = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException('Connexion à la base de données impossible : ' . $e->getMessage(), 0, $e);
            }
        }

        return self::$instance;
    }

    /**
     * Remplace l'instance PDO (utile pour les tests automatisés).
     */
    public static function setInstance(?PDO $pdo): void
    {
        self::$instance = $pdo;
    }
}
