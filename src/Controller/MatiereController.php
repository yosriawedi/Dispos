<?php

namespace App\Controller;

use App\Repository\MatiereRepository;
use App\Repository\SessionRevisionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/matieres')]
class MatiereController extends AbstractController
{
    #[Route('', name: 'app_matieres')]
    public function index(MatiereRepository $matiereRepo): Response
    {
        return $this->render('matiere/index.html.twig', [
            'matieres' => $matiereRepo->findActives(),
        ]);
    }

    #[Route('/{id}', name: 'app_matiere_show', requirements: ['id' => '\d+'])]
    public function show(int $id, MatiereRepository $matiereRepo, SessionRevisionRepository $sessionRepo): Response
    {
        $matiere = $matiereRepo->find($id);
        if (!$matiere) {
            throw $this->createNotFoundException('Matière introuvable');
        }

        return $this->render('matiere/show.html.twig', [
            'matiere' => $matiere,
            'sessions' => $sessionRepo->findByMatiere($id),
        ]);
    }
}
