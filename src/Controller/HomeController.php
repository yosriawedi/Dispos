<?php

namespace App\Controller;

use App\Entity\Startup;
use App\Entity\Project;
use App\Repository\StartupRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(StartupRepository $startupRepo, ProjectRepository $projectRepo, UserRepository $userRepo): Response
    {
        $latestStartups = $startupRepo->findLatest(6);
        $openProjects = $projectRepo->findOpenProjects(4);
        $talents = $userRepo->findByRole('ROLE_TALENT');
        $talentCount = count($talents);
        $startupCount = count($startupRepo->findAll());
        $investorCount = count($userRepo->findByRole('ROLE_INVESTOR'));

        return $this->render('home/index.html.twig', [
            'latestStartups' => $latestStartups,
            'openProjects' => $openProjects,
            'stats' => [
                'talents' => $talentCount,
                'startups' => $startupCount,
                'investors' => $investorCount,
            ],
        ]);
    }
}
