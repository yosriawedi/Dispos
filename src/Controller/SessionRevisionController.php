<?php

namespace App\Controller;

use App\Entity\InscriptionSession;
use App\Repository\InscriptionSessionRepository;
use App\Repository\SessionRevisionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sessions')]
class SessionRevisionController extends AbstractController
{
    #[Route('/{id}', name: 'app_session_show', requirements: ['id' => '\d+'])]
    public function show(int $id, SessionRevisionRepository $sessionRepo, InscriptionSessionRepository $inscriptionRepo): Response
    {
        $this->denyAccessIfFormateurOuEntreprise();

        $session = $sessionRepo->find($id);
        if (!$session) {
            throw $this->createNotFoundException('Session introuvable');
        }

        $dejaInscrit = false;
        if ($this->getUser()) {
            $dejaInscrit = $inscriptionRepo->findOneBy([
                'etudiant' => $this->getUser(),
                'session' => $session,
            ]) !== null;
        }

        return $this->render('session_revision/show.html.twig', [
            'session' => $session,
            'dejaInscrit' => $dejaInscrit,
        ]);
    }

    #[Route('/{id}/inscription', name: 'app_session_inscription', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ETUDIANT')]
    public function inscription(
        int $id,
        Request $request,
        SessionRevisionRepository $sessionRepo,
        InscriptionSessionRepository $inscriptionRepo,
        EntityManagerInterface $entityManager,
    ): Response {
        $session = $sessionRepo->find($id);
        if (!$session) {
            throw $this->createNotFoundException('Session introuvable');
        }

        if (!$this->isCsrfTokenValid('session_inscription_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Votre session a expiré — réessayez de vous inscrire.');
            return $this->redirectToRoute('app_session_show', ['id' => $id]);
        }

        $user = $this->getUser();
        $dejaInscrit = $inscriptionRepo->findOneBy(['etudiant' => $user, 'session' => $session]);

        if ($dejaInscrit) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à cette session.');
            return $this->redirectToRoute('app_session_show', ['id' => $id]);
        }

        if ($session->getPlacesRestantes() === 0) {
            $this->addFlash('error', 'Cette session est complète.');
            return $this->redirectToRoute('app_session_show', ['id' => $id]);
        }

        $methode = $request->request->get('methode');
        if (!\in_array($methode, [InscriptionSession::PAIEMENT_D17, InscriptionSession::PAIEMENT_RIB], true)) {
            $this->addFlash('error', 'Choisissez une méthode de paiement pour confirmer votre inscription.');
            return $this->redirectToRoute('app_session_show', ['id' => $id]);
        }

        $inscription = (new InscriptionSession())
            ->setEtudiant($user)
            ->setSession($session)
            ->setMethodePaiement($methode);

        $entityManager->persist($inscription);
        $entityManager->flush();

        $this->addFlash('success', 'Inscription confirmée pour "' . $session->getTitre() . '" !');

        return $this->redirectToRoute('app_session_show', ['id' => $id]);
    }

    /**
     * Le module Révision est réservé aux étudiants (et aux visiteurs non
     * connectés, pour la consultation) — formateur, entreprise, investisseur,
     * startup et talent sont hors de son périmètre, règle confirmée explicitement.
     */
    private function denyAccessIfFormateurOuEntreprise(): void
    {
        $user = $this->getUser();
        if (!$user || \in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return;
        }

        $roles = $user->getRoles();
        $rolesInterdits = ['ROLE_FORMATEUR', 'ROLE_ENTREPRISE', 'ROLE_INVESTOR', 'ROLE_STARTUP', 'ROLE_TALENT'];
        if (array_intersect($rolesInterdits, $roles)) {
            throw $this->createAccessDeniedException('Ce module n\'est pas accessible à ce rôle.');
        }
    }
}
