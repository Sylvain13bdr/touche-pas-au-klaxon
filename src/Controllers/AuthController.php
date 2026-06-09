<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Repositories\UtilisateurRepository;
use App\Services\AuthService;

/**
 * Authentification des utilisateurs (connexion / déconnexion).
 */
final class AuthController extends Controller
{
    /** Affiche le formulaire de connexion. */
    public function showLogin(): string
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        return $this->render('auth/login', ['title' => 'Connexion']);
    }

    /** Traite la soumission du formulaire de connexion. */
    public function login(): string
    {
        $email = trim((string) $this->post('email', ''));
        $motDePasse = (string) $this->post('password', '');

        $service = new AuthService(new UtilisateurRepository(Database::getInstance()));
        $utilisateur = $service->attempt($email, $motDePasse);

        if ($utilisateur === null) {
            Session::addFlash('danger', 'Adresse email ou mot de passe incorrect.');

            return $this->render('auth/login', ['title' => 'Connexion', 'email' => $email]);
        }

        Auth::login($utilisateur);
        Session::addFlash('success', 'Bienvenue ' . $utilisateur->prenom . ' !');
        $this->redirect('/');
    }

    /** Déconnecte l'utilisateur courant. */
    public function logout(): never
    {
        Auth::logout();
        Session::addFlash('success', 'Vous avez été déconnecté.');
        $this->redirect('/');
    }
}
