<?php

namespace App\Controller;

use App\Entity\CandidatureRecrutement;
use App\Entity\OffreRecrutement;
use App\Form\CandidatureRecrutementType;
use App\Form\OffreRecrutementType;
use App\Repository\CandidatureRecrutementRepository;
use App\Repository\OffreRecrutementRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/recrutement')]
class RecrutementController extends AbstractController
{
    #[Route('', name: 'app_recrutement')]
    public function index(OffreRecrutementRepository $offreRepo): Response
    {
        return $this->render('recrutement/index.html.twig', [
            'offres' => $offreRepo->findPubliees(),
        ]);
    }

    #[Route('/publier', name: 'app_recrutement_publier')]
    #[IsGranted('ROLE_ENTREPRISE')]
    public function publier(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new OffreRecrutement();
        $offre->setEntreprise($this->getUser());

        $form = $this->createForm(OffreRecrutementType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();

            $this->addFlash('success', 'Votre offre de recrutement a été publiée.');
            return $this->redirectToRoute('app_recrutement_mes_offres');
        }

        return $this->render('recrutement/publier.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/mes-offres', name: 'app_recrutement_mes_offres')]
    #[IsGranted('ROLE_ENTREPRISE')]
    public function mesOffres(OffreRecrutementRepository $offreRepo): Response
    {
        return $this->render('recrutement/mes_offres.html.twig', [
            'offres' => $offreRepo->findByEntreprise($this->getUser()->getId()),
        ]);
    }

    #[Route('/mes-candidatures', name: 'app_recrutement_mes_candidatures')]
    #[IsGranted('ROLE_USER')]
    public function mesCandidatures(CandidatureRecrutementRepository $candidatureRepo): Response
    {
        return $this->render('recrutement/mes_candidatures.html.twig', [
            'candidatures' => $candidatureRepo->findByCandidat($this->getUser()->getId()),
        ]);
    }

    #[Route('/mes-offres/{id}/candidatures', name: 'app_recrutement_offre_candidatures', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ENTREPRISE')]
    public function offreCandidatures(int $id, OffreRecrutementRepository $offreRepo): Response
    {
        $offre = $offreRepo->find($id);
        if (!$offre) {
            throw $this->createNotFoundException('Offre introuvable');
        }

        if ($offre->getEntreprise() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Cette offre ne vous appartient pas.');
        }

        return $this->render('recrutement/offre_candidatures.html.twig', [
            'offre' => $offre,
        ]);
    }

    #[Route('/{id}', name: 'app_recrutement_show', requirements: ['id' => '\d+'])]
    public function show(
        int $id,
        OffreRecrutementRepository $offreRepo,
        CandidatureRecrutementRepository $candidatureRepo,
    ): Response {
        $offre = $offreRepo->find($id);
        if (!$offre) {
            throw $this->createNotFoundException('Offre introuvable');
        }

        $dejaCandidate = false;
        if ($this->getUser()) {
            $dejaCandidate = $candidatureRepo->findOneBy([
                'candidat' => $this->getUser(),
                'offre' => $offre,
            ]) !== null;
        }

        return $this->render('recrutement/show.html.twig', [
            'offre' => $offre,
            'dejaCandidate' => $dejaCandidate,
            'form' => $this->createForm(CandidatureRecrutementType::class),
        ]);
    }

    #[Route('/{id}/candidater', name: 'app_recrutement_candidater', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function candidater(
        int $id,
        Request $request,
        OffreRecrutementRepository $offreRepo,
        CandidatureRecrutementRepository $candidatureRepo,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
    ): Response {
        $offre = $offreRepo->find($id);
        if (!$offre) {
            throw $this->createNotFoundException('Offre introuvable');
        }

        if ($candidatureRepo->findOneBy(['candidat' => $this->getUser(), 'offre' => $offre])) {
            $this->addFlash('error', 'Vous avez déjà candidaté à cette offre.');
            return $this->redirectToRoute('app_recrutement_show', ['id' => $id]);
        }

        $candidature = new CandidatureRecrutement();
        $candidature->setCandidat($this->getUser());
        $candidature->setOffre($offre);

        $form = $this->createForm(CandidatureRecrutementType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $cvFile */
            $cvFile = $form->get('cvFile')->getData();
            if ($cvFile) {
                $candidature->setCvFilename($fileUploader->upload($cvFile));
            }

            $entityManager->persist($candidature);
            $entityManager->flush();

            $this->addFlash('success', 'Votre candidature a été envoyée.');
            return $this->redirectToRoute('app_recrutement_show', ['id' => $id]);
        }

        $this->addFlash('error', 'Oups, quelque chose n\'a pas fonctionné — réessayez dans un instant.');
        return $this->redirectToRoute('app_recrutement_show', ['id' => $id]);
    }
}
