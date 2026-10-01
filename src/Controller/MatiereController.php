<?php

namespace App\Controller;

use App\Repository\MatiereRepository;
use App\Repository\SessionRevisionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/matieres')]
class MatiereController extends AbstractController
{
    #[Route('', name: 'app_matieres')]
    public function index(Request $request, MatiereRepository $matiereRepo): Response
    {
        $this->denyAccessIfFormateurOuEntreprise();

        $filiere = $request->query->get('filiere') ?: null;

        return $this->render('matiere/index.html.twig', [
            'result' => $matiereRepo->findActivesPaginated($request->query->getInt('page', 1), $filiere),
            'filieres' => $matiereRepo->findDistinctFilieres(),
            'currentFiliere' => $filiere,
        ]);
    }

    #[Route('/{id}', name: 'app_matiere_show', requirements: ['id' => '\d+'])]
    public function show(int $id, MatiereRepository $matiereRepo, SessionRevisionRepository $sessionRepo): Response
    {
        $this->denyAccessIfFormateurOuEntreprise();

        $matiere = $matiereRepo->find($id);
        if (!$matiere) {
            throw $this->createNotFoundException('Matière introuvable');
        }

        return $this->render('matiere/show.html.twig', [
            'matiere' => $matiere,
            'sessions' => $sessionRepo->findByMatiere($id),
        ]);
    }

    /**
     * Le module Révision n'est pas pertinent pour un formateur (il anime des
     * sessions, il ne les suit pas) ni pour une entreprise (hors de son
     * périmètre métier) — règle confirmée explicitement.
     */
    private function denyAccessIfFormateurOuEntreprise(): void
    {
        $user = $this->getUser();
        if (!$user || \in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return;
        }

        $roles = $user->getRoles();
        if (\in_array('ROLE_FORMATEUR', $roles, true) || \in_array('ROLE_ENTREPRISE', $roles, true)) {
            throw $this->createAccessDeniedException('Ce module n\'est pas accessible à ce rôle.');
        }
    }
}
