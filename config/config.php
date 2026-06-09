<?php

declare(strict_types=1);

/**
 * Configuration de l'application.
 *
 * Les valeurs par défaut correspondent à une installation XAMPP standard.
 * Pour adapter la connexion sans modifier ce fichier versionné, créez un
 * fichier config/config.local.php retournant un tableau ; il sera fusionné
 * par-dessus cette configuration (et il est ignoré par Git).
 *
 * @return array<string, array<string, scalar>>
 */

$config = [
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'name'    => 'touche_pas_au_klaxon',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name'  => 'Touche pas au klaxon',
        'debug' => true,
    ],
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    /** @var array<string, array<string, scalar>> $override */
    $override = require $local;
    $config = array_replace_recursive($config, $override);
}

return $config;
