<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Startup;
use App\Entity\Project;
use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span style="color:#6c63ff">Dis</span> Pos Admin')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('📊 Dashboard', 'fa fa-chart-line');
        yield MenuItem::section('Communauté');
        yield MenuItem::linkToCrud('👤 Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('🚀 Startups', 'fas fa-rocket', Startup::class);
        yield MenuItem::linkToCrud('📋 Projets', 'fas fa-clipboard', Project::class);
        yield MenuItem::linkToCrud('🏷️ Tags', 'fas fa-tag', Tag::class);
        yield MenuItem::section('Navigation');
        yield MenuItem::linkToUrl('🌐 Voir le site', 'fas fa-globe', '/');
    }
}
