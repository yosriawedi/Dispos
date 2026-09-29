<?php

namespace App\Controller;

use App\Entity\DemandeEncadrement;
use App\Form\DemandeEncadrementType;
use App\Repository\DemandeEncadrementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/encadrement')]
#[IsGranted('ROLE_USER')]
class DemandeEncadrementController extends AbstractController
{
    #[Route('/nouvelle', name: 'app_encadrement_nouvelle')]
    public function nouvelle(Request $request, EntityManagerInterface $entityManager): Response
    {
        $demande = new DemandeEncadrement();
        $demande->setEtudiant($this->getUser());

        $form = $this->createForm(DemandeEncadrementType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demande);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande d\'encadrement a bien été envoyée. L\'équipe DisPos va l\'étudier.');
            return $this->redirectToRoute('app_encadrement_mes_demandes');
        }

        return $this->render('encadrement/nouvelle.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/mes-demandes', name: 'app_encadrement_mes_demandes')]
    public function mesDemandes(DemandeEncadrementRepository $repo): Response
    {
        return $this->render('encadrement/mes_demandes.html.twig', [
            'demandes' => $repo->findByEtudiant($this->getUser()->getId()),
        ]);
    }
}
