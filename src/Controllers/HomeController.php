<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\TrajetRepository;

/**
 * Page d'accueil : liste des trajets à venir disposant de places,
 * accessible à tous (visiteurs et utilisateurs connectés).
 */
final class HomeController extends Controller
{
    public function index(): string
    {
        $repository = new TrajetRepository(Database::getInstance());
        $trajets = $repository->findUpcomingWithSeats();

        return $this->render('home/index', [
            'trajets' => $trajets,
            'title'   => 'Accueil',
        ]);
    }
}
