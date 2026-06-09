<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Contrôleur de base : fournit aux contrôleurs concrets les services
 * communs (rendu de vue, redirection, lecture des entrées, contrôle des
 * permissions).
 */
abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        Session::start();
        $this->view = new View();
    }

    /**
     * Rend une vue dans le layout en y injectant les données partagées
     * (nom de l'application, utilisateur courant, messages flash).
     *
     * @param array<string, mixed> $data
     */
    protected function render(string $template, array $data = []): string
    {
        /** @var array{app: array{name: string}} $config */
        $config = require dirname(__DIR__, 2) . '/config/config.php';

        $shared = [
            'appName'     => $config['app']['name'],
            'currentUser' => Auth::user(),
            'flashes'     => Session::takeFlashes(),
        ];

        return $this->view->render($template, array_merge($shared, $data));
    }

    /** Redirige vers un chemin interne et interrompt l'exécution. */
    protected function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    /** La requête courante est-elle de type POST ? */
    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /** Valeur d'un champ POST (chaîne), ou $default. */
    protected function post(string $key, ?string $default = null): ?string
    {
        $value = $_POST[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /** Valeur d'un paramètre GET (chaîne), ou $default. */
    protected function query(string $key, ?string $default = null): ?string
    {
        $value = $_GET[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /**
     * Données POST brutes (à transmettre aux services pour validation).
     *
     * @return array<string, mixed>
     */
    protected function postData(): array
    {
        /** @var array<string, mixed> $data */
        $data = $_POST;

        return $data;
    }

    /** Exige un utilisateur connecté, sinon redirige vers la connexion. */
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            Session::addFlash('warning', 'Veuillez vous connecter pour accéder à cette page.');
            $this->redirect('/login');
        }
    }

    /** Exige le rôle administrateur, sinon redirige vers l'accueil. */
    protected function requireAdmin(): void
    {
        $this->requireAuth();

        if (!Auth::isAdmin()) {
            Session::addFlash('danger', "Accès réservé à l'administrateur.");
            $this->redirect('/');
        }
    }
}
