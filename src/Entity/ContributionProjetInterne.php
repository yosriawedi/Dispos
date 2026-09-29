<?php

namespace App\Entity;

use App\Repository\ContributionProjetInterneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContributionProjetInterneRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_contribution', columns: ['etudiant_id', 'projet_interne_id'])]
class ContributionProjetInterne
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_ACCEPTEE   = 'acceptee';
    public const STATUT_REFUSEE    = 'refusee';
    public const STATUT_LIVREE     = 'livree';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'contributions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProjetInterneDispos $projetInterne = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message = null;

    #[ORM\Column(length: 20, options: ['default' => 'en_attente'])]
    private string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $noteAdmin = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getEtudiant(): ?User { return $this->etudiant; }
    public function setEtudiant(?User $u): static { $this->etudiant = $u; return $this; }
    public function getProjetInterne(): ?ProjetInterneDispos { return $this->projetInterne; }
    public function setProjetInterne(?ProjetInterneDispos $p): static { $this->projetInterne = $p; return $this; }
    public function getMessage(): ?string { return $this->message; }
    public function setMessage(?string $m): static { $this->message = $m; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; return $this; }
    public function getNoteAdmin(): ?string { return $this->noteAdmin; }
    public function setNoteAdmin(?string $n): static { $this->noteAdmin = $n; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }
}
