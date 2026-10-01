<?php

namespace App\Service;

use App\Entity\CandidatureFormateur;
use App\Entity\EtapeIncubation;
use App\Entity\User;
use App\Repository\CandidatureFormateurRepository;
use App\Repository\CandidatureRecrutementRepository;
use App\Repository\ContributionProjetInterneRepository;
use App\Repository\DemandeEncadrementRepository;
use App\Repository\IncubationRepository;
use App\Repository\InscriptionSessionRepository;
use App\Repository\OffreCompetenceRepository;
use App\Repository\OffreRecrutementRepository;
use App\Repository\SessionRevisionRepository;

/**
 * Statistiques adaptées au rôle d'un utilisateur, réutilisées par le
 * backoffice admin (fiche utilisateur) et le dashboard public (/dashboard).
 */
class UserStatsProvider
{
    public function __construct(
        private readonly DemandeEncadrementRepository $demandeEncadrementRepo,
        private readonly InscriptionSessionRepository $inscriptionSessionRepo,
        private readonly OffreCompetenceRepository $offreCompetenceRepo,
        private readonly ContributionProjetInterneRepository $contributionRepo,
        private readonly CandidatureFormateurRepository $candidatureFormateurRepo,
        private readonly SessionRevisionRepository $sessionRevisionRepo,
        private readonly OffreRecrutementRepository $offreRecrutementRepo,
        private readonly CandidatureRecrutementRepository $candidatureRecrutementRepo,
        private readonly IncubationRepository $incubationRepo,
    ) {
    }

    /**
     * @return list<array{key: string, label: string, value: int|string, icon: string, numeric: bool}>
     */
    public function getStatsForUser(User $user): array
    {
        $stats = match ($user->getPrimaryRole()) {
            User::ROLE_ETUDIANT => $this->etudiantStats($user),
            User::ROLE_FORMATEUR => $this->formateurStats($user),
            User::ROLE_ENTREPRISE => $this->entrepriseStats($user),
            default => [],
        };

        return array_map(
            static fn (array $stat) => $stat + ['numeric' => \is_int($stat['value'])],
            $stats
        );
    }

    private function etudiantStats(User $user): array
    {
        return [
            ['key' => 'sessions_suivies', 'label' => 'Sessions de révision suivies', 'value' => \count($this->inscriptionSessionRepo->findByEtudiant($user->getId())), 'icon' => 'fa-calendar-check'],
            ['key' => 'demandes_encadrement', 'label' => "Demandes d'encadrement soumises", 'value' => \count($this->demandeEncadrementRepo->findByEtudiant($user->getId())), 'icon' => 'fa-graduation-cap'],
            ['key' => 'offres_competence', 'label' => 'Offres de compétences soumises', 'value' => \count($this->offreCompetenceRepo->findByEtudiant($user->getId())), 'icon' => 'fa-handshake'],
            ['key' => 'contributions', 'label' => 'Contributions aux projets internes', 'value' => \count($this->contributionRepo->findByEtudiant($user->getId())), 'icon' => 'fa-toolbox'],
        ];
    }

    private function formateurStats(User $user): array
    {
        $candidatures = $this->candidatureFormateurRepo->findByCandidat($user->getId());
        $derniereCandidature = $candidatures[0] ?? null;

        return [
            ['key' => 'sessions_animees', 'label' => 'Sessions animées', 'value' => \count($this->sessionRevisionRepo->findByFormateur($user->getId())), 'icon' => 'fa-chalkboard-teacher'],
            ['key' => 'candidature_formateur', 'label' => 'Candidature formateur', 'value' => $derniereCandidature ? self::candidatureFormateurStatutLabel($derniereCandidature->getStatut()) : 'Aucune candidature', 'icon' => 'fa-clipboard-check'],
        ];
    }

    private function entrepriseStats(User $user): array
    {
        $offres = $this->offreRecrutementRepo->findByEntreprise($user->getId());
        $incubation = $this->incubationRepo->findActiveForEntreprise($user->getId());

        $avancementIncubation = 'Aucun dossier';
        if ($incubation) {
            $etapesTotal = $incubation->getEtapes()->count();
            $etapesValidees = $incubation->getEtapes()
                ->filter(static fn (EtapeIncubation $e) => $e->getStatut() === EtapeIncubation::STATUT_VALIDEE)
                ->count();
            $avancementIncubation = \sprintf('%d / %d étapes validées', $etapesValidees, $etapesTotal);
        }

        return [
            ['key' => 'offres_recrutement', 'label' => 'Offres de recrutement publiées', 'value' => \count($offres), 'icon' => 'fa-briefcase'],
            ['key' => 'candidatures_recues', 'label' => 'Candidatures reçues', 'value' => $this->candidatureRecrutementRepo->countByOffreEntreprise($user->getId()), 'icon' => 'fa-envelope-open-text'],
            ['key' => 'avancement_incubation', 'label' => "Avancement du dossier d'incubation", 'value' => $avancementIncubation, 'icon' => 'fa-building'],
        ];
    }

    private static function candidatureFormateurStatutLabel(string $statut): string
    {
        return match ($statut) {
            CandidatureFormateur::STATUT_SOUMISE => 'Soumise',
            CandidatureFormateur::STATUT_EN_REVIEW => 'En revue',
            CandidatureFormateur::STATUT_VALIDEE => 'Validée',
            CandidatureFormateur::STATUT_REFUSEE => 'Refusée',
            default => $statut,
        };
    }
}
