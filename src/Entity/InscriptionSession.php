<?php

namespace App\Entity;

use App\Repository\InscriptionSessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionSessionRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_inscription', columns: ['etudiant_id', 'session_id'])]
class InscriptionSession
{
    public const STATUT_CONFIRMEE  = 'confirmee';
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_ANNULEE    = 'annulee';

    public const PAIEMENT_D17 = 'd17';
    public const PAIEMENT_RIB = 'rib';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionRevision $session = null;

    #[ORM\Column(length: 20, options: ['default' => 'confirmee'])]
    private string $statut = self::STATUT_CONFIRMEE;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $noteEtudiant = null;

    /** Méthode choisie au moment de l'inscription — paiement simulé, aucune transaction réelle. */
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $methodePaiement = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getEtudiant(): ?User { return $this->etudiant; }
    public function setEtudiant(?User $u): static { $this->etudiant = $u; return $this; }
    public function getSession(): ?SessionRevision { return $this->session; }
    public function setSession(?SessionRevision $s): static { $this->session = $s; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; return $this; }
    public function getNoteEtudiant(): ?string { return $this->noteEtudiant; }
    public function setNoteEtudiant(?string $n): static { $this->noteEtudiant = $n; return $this; }
    public function getMethodePaiement(): ?string { return $this->methodePaiement; }
    public function setMethodePaiement(?string $m): static { $this->methodePaiement = $m; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }
}
