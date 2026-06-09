<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Trajet;
use App\Repositories\AgenceRepository;
use App\Repositories\TrajetRepository;
use App\Services\TrajetService;
use InvalidArgumentException;

/**
 * Gestion des trajets par les employés connectés : création, modification
 * et suppression de leurs propres trajets.
 */
final class TrajetController extends Controller
{
    /** Affiche le formulaire de création. */
    public function create(): string
    {
        $this->requireAuth();

        return $this->render('trajets/form', [
            'title'   => 'Créer un trajet',
            'agences' => $this->agenceRepository()->findAll(),
            'trajet'  => null,
            'errors'  => [],
            'old'     => [],
        ]);
    }

    /** Traite la création d'un trajet. */
    public function store(): string
    {
        $this->requireAuth();

        $data = $this->postData();
        $service = $this->trajetService();
        $errors = $service->validate($data);

        if ($errors !== []) {
            return $this->render('trajets/form', [
                'title'   => 'Créer un trajet',
                'agences' => $this->agenceRepository()->findAll(),
                'trajet'  => null,
                'errors'  => $errors,
                'old'     => $data,
            ]);
        }

        $service->create($data, Auth::id() ?? 0);
        Session::addFlash('success', 'Le trajet a été créé.');
        $this->redirect('/');
    }

    /** Affiche le formulaire de modification d'un trajet de l'utilisateur. */
    public function edit(): string
    {
        $this->requireAuth();
        $trajet = $this->findOwnedOrRedirect((int) $this->query('id', '0'));

        return $this->render('trajets/form', [
            'title'   => 'Modifier un trajet',
            'agences' => $this->agenceRepository()->findAll(),
            'trajet'  => $trajet,
            'errors'  => [],
            'old'     => [],
        ]);
    }

    /** Traite la modification d'un trajet. */
    public function update(): string
    {
        $this->requireAuth();
        $trajet = $this->findOwnedOrRedirect((int) $this->post('id', '0'));

        $data = $this->postData();
        $service = $this->trajetService();
        $errors = $service->validate($data);

        if ($errors !== []) {
            return $this->render('trajets/form', [
                'title'   => 'Modifier un trajet',
                'agences' => $this->agenceRepository()->findAll(),
                'trajet'  => $trajet,
                'errors'  => $errors,
                'old'     => $data,
            ]);
        }

        $service->update($trajet->id, $data);
        Session::addFlash('success', 'Le trajet a été modifié.');
        $this->redirect('/');
    }

    /** Supprime un trajet de l'utilisateur. */
    public function delete(): never
    {
        $this->requireAuth();
        $trajet = $this->findOwnedOrRedirect((int) $this->post('id', '0'));

        $this->trajetService()->delete($trajet->id);
        Session::addFlash('success', 'Le trajet a été supprimé.');
        $this->redirect('/');
    }

    /**
     * Récupère un trajet appartenant à l'utilisateur connecté, ou redirige
     * vers l'accueil avec un message si le trajet est introuvable ou n'est
     * pas le sien (contrôle d'autorisation côté serveur).
     */
    private function findOwnedOrRedirect(int $id): Trajet
    {
        $trajet = (new TrajetRepository(Database::getInstance()))->findById($id);

        if ($trajet === null) {
            Session::addFlash('danger', 'Trajet introuvable.');
            $this->redirect('/');
        }

        if (!$trajet->estAuteur(Auth::id() ?? 0)) {
            Session::addFlash('danger', 'Vous ne pouvez gérer que vos propres trajets.');
            $this->redirect('/');
        }

        return $trajet;
    }

    private function agenceRepository(): AgenceRepository
    {
        return new AgenceRepository(Database::getInstance());
    }

    private function trajetService(): TrajetService
    {
        $pdo = Database::getInstance();

        return new TrajetService(new TrajetRepository($pdo), new AgenceRepository($pdo));
    }
}
