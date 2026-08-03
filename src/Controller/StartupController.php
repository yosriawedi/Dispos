<?php

namespace App\Controller;

use App\Repository\StartupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/startups')]
class StartupController extends AbstractController
{
    #[Route('', name: 'app_startups')]
    public function index(StartupRepository $startupRepo, Request $request): Response
    {
        $sector = $request->query->get('sector');
        $startups = $sector ? $startupRepo->findBySector($sector) : $startupRepo->findAll();

        return $this->render('startup/index.html.twig', [
            'startups' => $startups,
            'currentSector' => $sector,
        ]);
    }

    #[Route('/{id}', name: 'app_startup_show')]
    public function show(int $id, StartupRepository $startupRepo): Response
    {
        $startup = $startupRepo->find($id);
        if (!$startup) {
            throw $this->createNotFoundException('Startup introuvable');
        }
        return $this->render('startup/show.html.twig', ['startup' => $startup]);
    }
}
