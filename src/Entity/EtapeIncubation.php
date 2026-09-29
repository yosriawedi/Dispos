<?php

namespace App\Entity;

use App\Repository\EtapeIncubationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Une des 7 phases du parcours d'incubation TPE/PME.
 */
#[ORM\Entity(repositoryClass: EtapeIncubationRepository::class)]
class EtapeIncubation
{
    public const PHASE_DIAGNOSTIC = 'diagnostic';
    public const PHASE_ETUDE_MARCHE = 'etude_marche';
    public const PHASE_STRUCTURATION_JURIDIQUE = 'structuration_juridique';
    public const PHASE_STRATEGIE_MARKETING = 'strategie_marketing';
    public const PHASE_DEVELOPPEMENT = 'developpement';
    public const PHASE_TEST_LANCEMENT = 'test_lancement';
    public const PHASE_SUIVI_POST_INCUBATION = 'suivi_post_incubation';

    /** Ordre canonique des 7 phases — utilisé pour la génération automatique et l'affichage timeline. */
    public const PHASES_ORDONNEES = [
        self::PHASE_DIAGNOSTIC,
        self::PHASE_ETUDE_MARCHE,
        self::PHASE_STRUCTURATION_JURIDIQUE,
        self::PHASE_STRATEGIE_MARKETING,
        self::PHASE_DEVELOPPEMENT,
        self::PHASE_TEST_LANCEMENT,
        self::PHASE_SUIVI_POST_INCUBATION,
    ];

    public const PHASE_LABELS = [
        self::PHASE_DIAGNOSTIC => 'Diagnostic',
        self::PHASE_ETUDE_MARCHE => 'Étude de marché',
        self::PHASE_STRUCTURATION_JURIDIQUE => 'Structuration juridique',
        self::PHASE_STRATEGIE_MARKETING => 'Stratégie marketing',
        self::PHASE_DEVELOPPEMENT => 'Développement',
        self::PHASE_TEST_LANCEMENT => 'Test & lancement',
        self::PHASE_SUIVI_POST_INCUBATION => 'Suivi post-incubation',
    ];

    public const STATUT_NON_DEMARREE = 'non_demarree';
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_EN_ATTENTE_VALIDATION = 'en_attente_validation';
    public const STATUT_VALIDEE = 'validee';
    public const STATUT_BLOQUEE = 'bloquee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'etapes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Incubation $incubation = null;

    #[ORM\Column(length: 30)]
    private ?string $phase = null;

    #[ORM\Column(length: 25, options: ['default' => 'non_demarree'])]
    private string $statut = self::STATUT_NON_DEMARREE;

    /** Personne affectée à cette phase — affectation manuelle et flexible, aucune contrainte de rôle. */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $referent = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaireAdmin = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateMiseAJour = null;

    public function __construct()
    {
        $this->dateMiseAJour = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getIncubation(): ?Incubation { return $this->incubation; }
    public function setIncubation(?Incubation $incubation): static { $this->incubation = $incubation; return $this; }
    public function getPhase(): ?string { return $this->phase; }
    public function setPhase(string $phase): static { $this->phase = $phase; return $this; }
    public function getPhaseLabel(): string { return self::PHASE_LABELS[$this->phase] ?? $this->phase; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; $this->dateMiseAJour = new \DateTimeImmutable(); return $this; }
    public function getReferent(): ?User { return $this->referent; }
    public function setReferent(?User $referent): static { $this->referent = $referent; $this->dateMiseAJour = new \DateTimeImmutable(); return $this; }
    public function getCommentaireAdmin(): ?string { return $this->commentaireAdmin; }
    public function setCommentaireAdmin(?string $commentaireAdmin): static { $this->commentaireAdmin = $commentaireAdmin; return $this; }
    public function getDateMiseAJour(): ?\DateTimeImmutable { return $this->dateMiseAJour; }
    public function setDateMiseAJour(\DateTimeImmutable $dateMiseAJour): static { $this->dateMiseAJour = $dateMiseAJour; return $this; }

    public function __toString(): string
    {
        return $this->getPhaseLabel();
    }
}
