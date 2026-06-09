<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Repositories\AgenceRepository;
use App\Repositories\TrajetRepository;
use App\Repositories\UtilisateurRepository;
use App\Services\AgenceService;
use App\Services\TrajetService;

/**
 * Tableau de bord de l'administrateur : consultation des utilisateurs,
 * gestion complète des agences et des trajets. Toutes les actions sont
 * réservées au rôle « admin ».
 */
final class AdminController extends Controller
{
    /** Tableau de bord : vue d'ensemble avec accès aux sections. */
    public function index(): string
    {
        $this->requireAdmin();
        $pdo = Database::getInstance();

        return $this->render('admin/dashboard', [
            'title'          => 'Tableau de bord',
            'nbUtilisateurs' => count((new UtilisateurRepository($pdo))->findAll()),
            'nbAgences'      => count((new AgenceRepository($pdo))->findAll()),
            'nbTrajets'      => count((new TrajetRepository($pdo))->findAll()),
        ]);
    }

    /** Liste des utilisateurs. */
    public function utilisateurs(): string
    {
        $this->requireAdmin();

        return $this->render('admin/utilisateurs', [
            'title'        => 'Utilisateurs',
            'utilisateurs' => (new UtilisateurRepository(Database::getInstance()))->findAll(),
        ]);
    }

    /** Liste et gestion des agences. */
    public function agences(): string
    {
        $this->requireAdmin();

        return $this->render('admin/agences', [
            'title'   => 'Agences',
            'agences' => (new AgenceRepository(Database::getInstance()))->findAll(),
        ]);
    }

    /** Crée une agence. */
    public function creerAgence(): never
    {
        $this->requireAdmin();
        $service = new AgenceService(new AgenceRepository(Database::getInstance()));
        $errors = $service->validate($this->postData());

        if ($errors !== []) {
            Session::addFlash('danger', implode(' ', $errors));
        } else {
            $service->create($this->postData());
            Session::addFlash('success', "L'agence a été créée.");
        }

        $this->redirect('/admin/agences');
    }

    /** Modifie une agence. */
    public function modifierAgence(): never
    {
        $this->requireAdmin();
        $id = (int) $this->post('id', '0');
        $service = new AgenceService(new AgenceRepository(Database::getInstance()));
        $errors = $service->validate($this->postData(), $id);

        if ($errors !== []) {
            Session::addFlash('danger', implode(' ', $errors));
        } else {
            $service->update($id, $this->postData());
            Session::addFlash('success', "L'agence a été modifiée.");
        }

        $this->redirect('/admin/agences');
    }

    /** Supprime une agence (refusée si des trajets y sont rattachés). */
    public function supprimerAgence(): never
    {
        $this->requireAdmin();
        $id = (int) $this->post('id', '0');
        $service = new AgenceService(new AgenceRepository(Database::getInstance()));

        if ($service->delete($id)) {
            Session::addFlash('success', "L'agence a été supprimée.");
        } else {
            Session::addFlash('danger', "Impossible de supprimer une agence rattachée à des trajets.");
        }

        $this->redirect('/admin/agences');
    }

    /** Liste de tous les trajets. */
    public function trajets(): string
    {
        $this->requireAdmin();

        return $this->render('admin/trajets', [
            'title'   => 'Trajets',
            'trajets' => (new TrajetRepository(Database::getInstance()))->findAll(),
        ]);
    }

    /** Supprime n'importe quel trajet. */
    public function supprimerTrajet(): never
    {
        $this->requireAdmin();
        $id = (int) $this->post('id', '0');
        $pdo = Database::getInstance();
        (new TrajetService(new TrajetRepository($pdo), new AgenceRepository($pdo)))->delete($id);
        Session::addFlash('success', 'Le trajet a été supprimé.');

        $this->redirect('/admin/trajets');
    }
}
