<?php

namespace App\Entity;

use App\Repository\DemandeReductionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Demande de réduction liée à une OffreCompetence validée.
 * Montant/calcul TBD — seul le statut est géré ici.
 */
#[ORM\Entity(repositoryClass: DemandeReductionRepository::class)]
class DemandeReduction
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_APPROUVEE  = 'approuvee';
    public const STATUT_REFUSEE    = 'refusee';
    public const STATUT_APPLIQUEE  = 'appliquee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $etudiant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?OffreCompetence $offreCompetence = null;

    /** Prestation DisPos ciblée par la réduction */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contextePrestationCiblee = null;

    /**
     * Statut extensible — pas de calcul automatique tant que le barème n'est pas validé.
     */
    #[ORM\Column(length: 20, options: ['default' => 'en_attente'])]
    private string $statut = self::STATUT_EN_ATTENTE;

    /**
     * Champ JSON libre pour futures métadonnées (valeur calculée, barème appliqué…).
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadonnees = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $messageAdmin = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getEtudiant(): ?User { return $this->etudiant; }
    public function setEtudiant(?User $u): static { $this->etudiant = $u; return $this; }
    public function getOffreCompetence(): ?OffreCompetence { return $this->offreCompetence; }
    public function setOffreCompetence(?OffreCompetence $o): static { $this->offreCompetence = $o; return $this; }
    public function getContextePrestationCiblee(): ?string { return $this->contextePrestationCiblee; }
    public function setContextePrestationCiblee(?string $c): static { $this->contextePrestationCiblee = $c; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; $this->updatedAt = new \DateTimeImmutable(); return $this; }
    public function getMetadonnees(): ?array { return $this->metadonnees; }
    public function setMetadonnees(?array $m): static { $this->metadonnees = $m; return $this; }
    public function getMessageAdmin(): ?string { return $this->messageAdmin; }
    public function setMessageAdmin(?string $m): static { $this->messageAdmin = $m; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
