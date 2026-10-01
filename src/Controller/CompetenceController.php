<?php

namespace App\Controller;

use App\Entity\DemandeReduction;
use App\Entity\OffreCompetence;
use App\Form\DemandeReductionType;
use App\Form\OffreCompetenceType;
use App\Repository\DemandeReductionRepository;
use App\Repository\OffreCompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/competences')]
#[IsGranted('ROLE_USER')]
class CompetenceController extends AbstractController
{
    #[Route('/nouvelle', name: 'app_competence_nouvelle')]
    #[IsGranted('ROLE_ETUDIANT')]
    public function nouvelle(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new OffreCompetence();
        $offre->setEtudiant($this->getUser());

        $form = $this->createForm(OffreCompetenceType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();

            $this->addFlash('success', 'Votre offre de compétence a bien été soumise pour validation.');
            return $this->redirectToRoute('app_competence_mes_offres');
        }

        return $this->render('competence/nouvelle.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/mes-offres', name: 'app_competence_mes_offres')]
    #[IsGranted('ROLE_ETUDIANT')]
    public function mesOffres(
        OffreCompetenceRepository $offreRepo,
        DemandeReductionRepository $reductionRepo,
    ): Response {
        $offres = $offreRepo->findByEtudiant($this->getUser()->getId());
        $reductions = $reductionRepo->findByEtudiant($this->getUser()->getId());

        $reductionParOffre = [];
        foreach ($reductions as $reduction) {
            $reductionParOffre[$reduction->getOffreCompetence()->getId()] = $reduction;
        }

        return $this->render('competence/mes_offres.html.twig', [
            'offres' => $offres,
            'reductionParOffre' => $reductionParOffre,
        ]);
    }

    #[Route('/{id}/demande-reduction', name: 'app_competence_demande_reduction', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ETUDIANT')]
    public function demandeReduction(
        int $id,
        Request $request,
        OffreCompetenceRepository $offreRepo,
        DemandeReductionRepository $reductionRepo,
        EntityManagerInterface $entityManager,
    ): Response {
        $offre = $offreRepo->find($id);
        if (!$offre || $offre->getEtudiant() !== $this->getUser()) {
            throw $this->createNotFoundException('Offre introuvable');
        }

        if ($offre->getStatut() !== OffreCompetence::STATUT_VALIDEE) {
            $this->addFlash('error', 'Cette offre de compétence doit être validée par DisPos avant de demander une réduction.');
            return $this->redirectToRoute('app_competence_mes_offres');
        }

        if ($reductionRepo->findOneBy(['offreCompetence' => $offre])) {
            $this->addFlash('error', 'Une demande de réduction existe déjà pour cette offre.');
            return $this->redirectToRoute('app_competence_mes_offres');
        }

        $reduction = new DemandeReduction();
        $reduction->setEtudiant($this->getUser());
        $reduction->setOffreCompetence($offre);

        $form = $this->createForm(DemandeReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reduction);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande de réduction a été envoyée. Le barème sera appliqué par l\'équipe DisPos.');
            return $this->redirectToRoute('app_competence_mes_offres');
        }

        return $this->render('competence/demande_reduction.html.twig', [
            'form' => $form,
            'offre' => $offre,
        ]);
    }
}
