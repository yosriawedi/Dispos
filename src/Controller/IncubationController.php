<?php

namespace App\Controller;

use App\Entity\DemandeConsultation;
use App\Entity\Incubation;
use App\Form\DemandeConsultationType;
use App\Form\IncubationCreationType;
use App\Repository\IncubationRepository;
use App\Security\Voter\IncubationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mon-espace/incubation')]
#[IsGranted('ROLE_USER')]
class IncubationController extends AbstractController
{
    #[Route('', name: 'app_incubation_suivi')]
    #[IsGranted('ROLE_ENTREPRISE')]
    public function suivi(IncubationRepository $incubationRepo): Response
    {
        $incubation = $incubationRepo->findActiveForEntreprise($this->getUser()->getId());

        if (!$incubation) {
            return $this->render('incubation/creer.html.twig', [
                'form' => $this->createForm(IncubationCreationType::class),
            ]);
        }

        // Toujours son propre dossier actif ici — le Voter est appliqué quand
        // même pour rester l'unique point d'autorité sur "qui peut voir quoi".
        $this->denyAccessUnlessGranted(IncubationVoter::VIEW, $incubation);

        return $this->render('incubation/suivi.html.twig', [
            'incubation' => $incubation,
            'consultationForm' => $this->createForm(DemandeConsultationType::class),
        ]);
    }

    #[Route('/{id}', name: 'app_incubation_show', requirements: ['id' => '\d+'])]
    public function show(int $id, IncubationRepository $incubationRepo): Response
    {
        $incubation = $incubationRepo->find($id);
        if (!$incubation) {
            throw $this->createNotFoundException('Dossier d\'incubation introuvable');
        }

        // Accès par identifiant : ici le Voter refuse réellement (403) si le
        // dossier n'appartient pas à l'utilisateur connecté (et n'est pas admin).
        $this->denyAccessUnlessGranted(IncubationVoter::VIEW, $incubation);

        return $this->render('incubation/suivi.html.twig', [
            'incubation' => $incubation,
            'consultationForm' => $this->createForm(DemandeConsultationType::class),
        ]);
    }

    #[Route('/creer', name: 'app_incubation_creer', methods: ['POST'])]
    #[IsGranted('ROLE_ENTREPRISE')]
    public function creer(Request $request, IncubationRepository $incubationRepo, EntityManagerInterface $entityManager): Response
    {
        if ($incubationRepo->findActiveForEntreprise($this->getUser()->getId())) {
            $this->addFlash('error', 'Vous avez déjà un dossier d\'incubation actif.');
            return $this->redirectToRoute('app_incubation_suivi');
        }

        $incubation = new Incubation();
        $incubation->setEntreprise($this->getUser());

        $form = $this->createForm(IncubationCreationType::class, $incubation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($incubation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre dossier d\'incubation a été créé. Les 7 phases du parcours sont prêtes à démarrer.');
            return $this->redirectToRoute('app_incubation_suivi');
        }

        $this->addFlash('error', 'Oups, quelque chose n\'a pas fonctionné — réessayez dans un instant.');
        return $this->redirectToRoute('app_incubation_suivi');
    }

    #[Route('/demande-consultation', name: 'app_incubation_demande_consultation', methods: ['POST'])]
    #[IsGranted('ROLE_ENTREPRISE')]
    public function demandeConsultation(
        Request $request,
        IncubationRepository $incubationRepo,
        EntityManagerInterface $entityManager,
    ): Response {
        $incubation = $incubationRepo->findActiveForEntreprise($this->getUser()->getId());
        if (!$incubation) {
            throw $this->createNotFoundException('Aucun dossier d\'incubation actif');
        }

        $this->denyAccessUnlessGranted(IncubationVoter::VIEW, $incubation);

        $demande = new DemandeConsultation();
        $demande->setIncubation($incubation);

        $form = $this->createForm(DemandeConsultationType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demande);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande de consultation a été envoyée à l\'équipe DisPos.');
            return $this->redirectToRoute('app_incubation_suivi');
        }

        $this->addFlash('error', 'Oups, quelque chose n\'a pas fonctionné — réessayez dans un instant.');
        return $this->redirectToRoute('app_incubation_suivi');
    }
}
