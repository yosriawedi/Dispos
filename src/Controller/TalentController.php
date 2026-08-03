<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TalentController extends AbstractController
{
    #[Route('/talents', name: 'app_talents')]
    public function index(UserRepository $userRepo): Response
    {
        $talents = $userRepo->findByRole('ROLE_TALENT');
        return $this->render('talent/index.html.twig', ['talents' => $talents]);
    }
}
