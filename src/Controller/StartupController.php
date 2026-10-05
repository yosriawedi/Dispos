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
        $this->denyAccessIfEntreprise();

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
        $this->denyAccessIfEntreprise();

        $startup = $startupRepo->find($id);
        if (!$startup) {
            throw $this->createNotFoundException('Startup introuvable');
        }
        return $this->render('startup/show.html.twig', ['startup' => $startup]);
    }

    /**
     * L'entreprise suit son propre dossier d'incubation (module dédié) —
     * le hub Startups (vitrine des startups tierces) n'est pas son périmètre.
     * Une startup n'a pas besoin de consulter son propre hub non plus —
     * règle confirmée explicitement.
     */
    private function denyAccessIfEntreprise(): void
    {
        $user = $this->getUser();
        if (!$user) {
            return;
        }

        $roles = $user->getRoles();
        if (\in_array('ROLE_ENTREPRISE', $roles, true) || \in_array('ROLE_STARTUP', $roles, true)) {
            throw $this->createAccessDeniedException('Ce module n\'est pas accessible à ce rôle.');
        }
    }
}
