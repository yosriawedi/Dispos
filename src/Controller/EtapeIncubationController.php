<?php

namespace App\Controller;

use App\Form\EtapeIncubationUpdateType;
use App\Repository\EtapeIncubationRepository;
use App\Security\Voter\EtapeIncubationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Espace référent : n'importe quel rôle peut se voir affecter une étape
 * d'incubation (Ticket 1.2 — affectation manuelle et flexible) et doit
 * pouvoir la mettre à jour sans passer par /admin.
 */
#[Route('/mes-affectations/incubation')]
#[IsGranted('ROLE_USER')]
class EtapeIncubationController extends AbstractController
{
    #[Route('', name: 'app_etape_incubation_mes_affectations')]
    public function mesAffectations(EtapeIncubationRepository $etapeRepo): Response
    {
        return $this->render('incubation/mes_affectations.html.twig', [
            'etapes' => $etapeRepo->findByReferent($this->getUser()->getId()),
        ]);
    }

    #[Route('/{id}', name: 'app_etape_incubation_maj', requirements: ['id' => '\d+'])]
    public function maj(
        int $id,
        Request $request,
        EtapeIncubationRepository $etapeRepo,
        EntityManagerInterface $entityManager,
    ): Response {
        $etape = $etapeRepo->find($id);
        if (!$etape) {
            throw $this->createNotFoundException('Étape introuvable');
        }

        $this->denyAccessUnlessGranted(EtapeIncubationVoter::EDIT, $etape);

        $form = $this->createForm(EtapeIncubationUpdateType::class, $etape);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Étape mise à jour.');
            return $this->redirectToRoute('app_etape_incubation_mes_affectations');
        }

        return $this->render('incubation/maj_etape.html.twig', [
            'etape' => $etape,
            'form' => $form,
        ]);
    }
}
