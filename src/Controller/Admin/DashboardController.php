<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Startup;
use App\Entity\Project;
use App\Entity\Tag;
use App\Entity\Matiere;
use App\Entity\SessionRevision;
use App\Entity\InscriptionSession;
use App\Entity\DemandeEncadrement;
use App\Entity\OffreCompetence;
use App\Entity\DemandeReduction;
use App\Entity\ProjetInterneDispos;
use App\Entity\ContributionProjetInterne;
use App\Entity\CandidatureFormateur;
use App\Entity\OffreRecrutement;
use App\Entity\CandidatureRecrutement;
use App\Entity\Incubation;
use App\Entity\EtapeIncubation;
use App\Entity\DemandeConsultation;
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

        yield MenuItem::section('Académique — Révision');
        yield MenuItem::linkToCrud('📚 Matières', 'fas fa-book', Matiere::class);
        yield MenuItem::linkToCrud('🗓️ Sessions de révision', 'fas fa-calendar', SessionRevision::class);
        yield MenuItem::linkToCrud('✅ Inscriptions', 'fas fa-user-check', InscriptionSession::class);

        yield MenuItem::section('Académique — Incubation');
        yield MenuItem::linkToCrud('🎓 Demandes d\'encadrement', 'fas fa-graduation-cap', DemandeEncadrement::class);
        yield MenuItem::linkToCrud('🤝 Offres de compétences', 'fas fa-handshake', OffreCompetence::class);
        yield MenuItem::linkToCrud('🎁 Demandes de réduction', 'fas fa-gift', DemandeReduction::class);
        yield MenuItem::linkToCrud('🛠️ Projets internes', 'fas fa-toolbox', ProjetInterneDispos::class);
        yield MenuItem::linkToCrud('📥 Contributions', 'fas fa-inbox', ContributionProjetInterne::class);

        yield MenuItem::section('Incubation TPE/PME');
        yield MenuItem::linkToCrud('🏢 Dossiers d\'incubation', 'fas fa-building', Incubation::class);
        yield MenuItem::linkToCrud('🗺️ Étapes d\'incubation', 'fas fa-route', EtapeIncubation::class);
        yield MenuItem::linkToCrud('📞 Demandes de consultation', 'fas fa-comments', DemandeConsultation::class);

        yield MenuItem::section('Formateurs & Recrutement');
        yield MenuItem::linkToCrud('👨‍🏫 Candidatures formateurs', 'fas fa-chalkboard-teacher', CandidatureFormateur::class);
        yield MenuItem::linkToCrud('💼 Offres de recrutement', 'fas fa-briefcase', OffreRecrutement::class);
        yield MenuItem::linkToCrud('📨 Candidatures recrutement', 'fas fa-envelope-open-text', CandidatureRecrutement::class);

        yield MenuItem::section('Communauté');
        yield MenuItem::linkToCrud('👤 Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('🚀 Startups', 'fas fa-rocket', Startup::class);
        yield MenuItem::linkToCrud('📋 Projets', 'fas fa-clipboard', Project::class);
        yield MenuItem::linkToCrud('🏷️ Tags', 'fas fa-tag', Tag::class);
        yield MenuItem::section('Navigation');
        yield MenuItem::linkToUrl('🌐 Voir le site', 'fas fa-globe', '/');
    }
}
