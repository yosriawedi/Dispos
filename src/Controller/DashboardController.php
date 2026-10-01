<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserStatsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly UserStatsProvider $userStatsProvider,
    ) {
    }

    #[Route('', name: 'app_dashboard')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $stats = $this->userStatsProvider->getStatsForUser($user);

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'statsByKey' => array_column($stats, null, 'key'),
        ]);
    }
}
