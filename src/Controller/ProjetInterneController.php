<?php

namespace App\Controller;

use App\Entity\ContributionProjetInterne;
use App\Form\ContributionProjetInterneType;
use App\Repository\ContributionProjetInterneRepository;
use App\Repository\ProjetInterneDisposRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projets-internes')]
class ProjetInterneController extends AbstractController
{
    #[Route('', name: 'app_projets_internes')]
    public function index(Request $request, ProjetInterneDisposRepository $projetRepo): Response
    {
        $domaine = $request->query->get('domaine') ?: null;

        return $this->render('projet_interne/index.html.twig', [
            'result' => $projetRepo->findOuvertsPaginated($request->query->getInt('page', 1), $domaine),
            'domaines' => $projetRepo->findDistinctDomaines(),
            'currentDomaine' => $domaine,
        ]);
    }

    #[Route('/mes-contributions', name: 'app_projets_internes_mes_contributions')]
    #[IsGranted('ROLE_USER')]
    public function mesContributions(ContributionProjetInterneRepository $contributionRepo): Response
    {
        return $this->render('projet_interne/mes_contributions.html.twig', [
            'contributions' => $contributionRepo->findByEtudiant($this->getUser()->getId()),
        ]);
    }

    #[Route('/{id}', name: 'app_projet_interne_show', requirements: ['id' => '\d+'])]
    public function show(
        int $id,
        ProjetInterneDisposRepository $projetRepo,
        ContributionProjetInterneRepository $contributionRepo,
    ): Response {
        $projet = $projetRepo->find($id);
        if (!$projet) {
            throw $this->createNotFoundException('Projet introuvable');
        }

        $dejaContribue = false;
        if ($this->getUser()) {
            $dejaContribue = $contributionRepo->findOneBy([
                'etudiant' => $this->getUser(),
                'projetInterne' => $projet,
            ]) !== null;
        }

        return $this->render('projet_interne/show.html.twig', [
            'projet' => $projet,
            'dejaContribue' => $dejaContribue,
            'form' => $this->createForm(ContributionProjetInterneType::class),
        ]);
    }

    #[Route('/{id}/contribuer', name: 'app_projet_interne_contribuer', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function contribuer(
        int $id,
        Request $request,
        ProjetInterneDisposRepository $projetRepo,
        ContributionProjetInterneRepository $contributionRepo,
        EntityManagerInterface $entityManager,
    ): Response {
        $projet = $projetRepo->find($id);
        if (!$projet) {
            throw $this->createNotFoundException('Projet introuvable');
        }

        if ($contributionRepo->findOneBy(['etudiant' => $this->getUser(), 'projetInterne' => $projet])) {
            $this->addFlash('error', 'Vous avez déjà proposé une contribution pour ce projet.');
            return $this->redirectToRoute('app_projet_interne_show', ['id' => $id]);
        }

        if ($projet->getPlacesRestantes() === 0) {
            $this->addFlash('error', 'Ce projet n\'a plus de places disponibles.');
            return $this->redirectToRoute('app_projet_interne_show', ['id' => $id]);
        }

        $contribution = new ContributionProjetInterne();
        $contribution->setEtudiant($this->getUser());
        $contribution->setProjetInterne($projet);

        $form = $this->createForm(ContributionProjetInterneType::class, $contribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contribution);
            $entityManager->flush();

            $this->addFlash('success', 'Votre proposition de contribution a été envoyée.');
            return $this->redirectToRoute('app_projet_interne_show', ['id' => $id]);
        }

        $this->addFlash('error', 'Oups, quelque chose n\'a pas fonctionné — réessayez dans un instant.');
        return $this->redirectToRoute('app_projet_interne_show', ['id' => $id]);
    }
}
