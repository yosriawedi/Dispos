<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    #[Route('/projects', name: 'app_projects')]
    public function index(ProjectRepository $projectRepo): Response
    {
        $user = $this->getUser();
        if ($user && \in_array('ROLE_INVESTOR', $user->getRoles(), true)) {
            throw $this->createAccessDeniedException('Ce module n\'est pas accessible à ce rôle.');
        }

        $projects = $projectRepo->findOpenProjects(20);
        return $this->render('project/index.html.twig', ['projects' => $projects]);
    }
}
