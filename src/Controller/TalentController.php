<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TalentController extends AbstractController
{
    #[Route('/talents', name: 'app_talents')]
    public function index(UserRepository $userRepo): Response
    {
        // Un investisseur n'a accès qu'aux projets internes et aux startups ;
        // un talent n'a pas besoin de consulter son propre hub — règle confirmée explicitement.
        $roles = $this->getUser()->getRoles();
        if (\in_array('ROLE_INVESTOR', $roles, true) || \in_array('ROLE_TALENT', $roles, true)) {
            throw $this->createAccessDeniedException('Ce module n\'est pas accessible à ce rôle.');
        }

        $talents = $userRepo->findByRole('ROLE_TALENT');
        return $this->render('talent/index.html.twig', ['talents' => $talents]);
    }
}
