<?php

declare(strict_types=1);

/**
 * Front controller : point d'entrée unique de l'application.
 * Toutes les requêtes HTTP sont routées ici puis dispatchées vers les
 * contrôleurs adéquats par le routeur.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Session;
use Buki\Router\Router;

// Sous le serveur web intégré de PHP (php -S), on laisse le serveur servir
// directement les fichiers statiques existants (CSS, JS) sans passer par le
// routeur applicatif.
if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (is_string($requestPath) && $requestPath !== '/' && is_file(__DIR__ . $requestPath)) {
        return false;
    }
}

Session::start();

/** @var array{app: array{debug: bool}} $config */
$config = require dirname(__DIR__) . '/config/config.php';

$router = new Router([
    'debug'      => $config['app']['debug'],
    'paths'      => [
        'controllers' => dirname(__DIR__) . '/src/Controllers',
        'middlewares' => dirname(__DIR__) . '/src/Middlewares',
    ],
    'namespaces' => [
        'controllers' => 'App\\Controllers',
        'middlewares' => 'App\\Middlewares',
    ],
    'base_folder' => dirname(__DIR__),
    'main_method' => 'index',
]);

// --- Accueil / liste des trajets (public et connecté) ---
$router->get('/', 'HomeController@index');

// --- Authentification ---
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// --- Trajets (employé connecté) ---
$router->get('/trajets/creer', 'TrajetController@create');
$router->post('/trajets/creer', 'TrajetController@store');
$router->get('/trajets/modifier', 'TrajetController@edit');
$router->post('/trajets/modifier', 'TrajetController@update');
$router->post('/trajets/supprimer', 'TrajetController@delete');

// --- Tableau de bord administrateur ---
$router->get('/admin', 'AdminController@index');
$router->get('/admin/utilisateurs', 'AdminController@utilisateurs');
$router->get('/admin/agences', 'AdminController@agences');
$router->post('/admin/agences/creer', 'AdminController@creerAgence');
$router->post('/admin/agences/modifier', 'AdminController@modifierAgence');
$router->post('/admin/agences/supprimer', 'AdminController@supprimerAgence');
$router->get('/admin/trajets', 'AdminController@trajets');
$router->post('/admin/trajets/supprimer', 'AdminController@supprimerTrajet');

// --- Page 404 personnalisée ---
$router->error(function () {
    http_response_code(404);

    return '<!doctype html><html lang="fr"><head><meta charset="utf-8">'
        . '<title>Page introuvable</title></head><body style="font-family:sans-serif;text-align:center;padding:3rem">'
        . '<h1>404</h1><p>La page demandée est introuvable.</p>'
        . '<p><a href="/">Retour à l\'accueil</a></p></body></html>';
});

$router->run();
