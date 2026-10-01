<?php

namespace App\Controller;

use App\Entity\CandidatureFormateur;
use App\Form\CandidatureFormateurType;
use App\Repository\CandidatureFormateurRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/formateurs')]
#[IsGranted('ROLE_USER')]
class FormateurController extends AbstractController
{
    #[Route('/postuler', name: 'app_formateur_postuler')]
    public function postuler(
        Request $request,
        CandidatureFormateurRepository $candidatureRepo,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
    ): Response {
        $this->denyAccessUnlessEtudiantOuTalent();

        $existante = $candidatureRepo->findOneBy(['candidat' => $this->getUser()]);
        if ($existante) {
            return $this->redirectToRoute('app_formateur_ma_candidature');
        }

        $candidature = new CandidatureFormateur();
        $candidature->setCandidat($this->getUser());

        $form = $this->createForm(CandidatureFormateurType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $cvFile */
            $cvFile = $form->get('cvFile')->getData();
            if ($cvFile) {
                $candidature->setCvFilename($fileUploader->upload($cvFile));
            }

            $entityManager->persist($candidature);
            $entityManager->flush();

            $this->addFlash('success', 'Votre candidature formateur a bien été envoyée.');
            return $this->redirectToRoute('app_formateur_ma_candidature');
        }

        return $this->render('formateur/postuler.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ma-candidature', name: 'app_formateur_ma_candidature')]
    public function maCandidature(CandidatureFormateurRepository $candidatureRepo): Response
    {
        // Consulter sa propre candidature reste possible même après être
        // devenu formateur (ROLE_FORMATEUR) — seule la création (postuler)
        // est réservée aux étudiants/talents. Créer ≠ consulter.
        $candidature = $candidatureRepo->findOneBy(['candidat' => $this->getUser()]);

        return $this->render('formateur/ma_candidature.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    /**
     * Seuls les étudiants et les talents peuvent postuler pour devenir
     * formateur (règle métier confirmée) — ni une entreprise, ni un
     * formateur déjà en poste, ni l'admin (qui gère tout via /admin).
     */
    private function denyAccessUnlessEtudiantOuTalent(): void
    {
        $roles = $this->getUser()->getRoles();
        if (!\in_array('ROLE_ETUDIANT', $roles, true) && !\in_array('ROLE_TALENT', $roles, true)) {
            throw $this->createAccessDeniedException('Seuls les étudiants et les talents peuvent postuler pour devenir formateur.');
        }
    }
}
