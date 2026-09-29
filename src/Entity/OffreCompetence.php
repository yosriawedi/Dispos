<?php

namespace App\Entity;

use App\Repository\OffreCompetenceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Compétence proposée par un étudiant en échange d'une réduction.
 * Logique de calcul TBD — ne pas figer le barème.
 */
#[ORM\Entity(repositoryClass: OffreCompetenceRepository::class)]
class OffreCompetence
{
    public const STATUT_SOUMISE = 'soumise';
    public const STATUT_VALIDEE = 'validee';
    public const STATUT_REFUSEE = 'refusee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $etudiant = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $stack = null;

    /** Estimation en heures fournie par l'étudiant — barème de conversion TBD */
    #[ORM\Column(nullable: true)]
    private ?int $heuresEstimees = null;

    #[ORM\Column(length: 20, options: ['default' => 'soumise'])]
    private string $statut = self::STATUT_SOUMISE;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $noteAdmin = null;

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
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $t): static { $this->titre = $t; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $d): static { $this->description = $d; return $this; }
    public function getStack(): ?string { return $this->stack; }
    public function setStack(?string $s): static { $this->stack = $s; return $this; }
    public function getHeuresEstimees(): ?int { return $this->heuresEstimees; }
    public function setHeuresEstimees(?int $h): static { $this->heuresEstimees = $h; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; $this->updatedAt = new \DateTimeImmutable(); return $this; }
    public function getNoteAdmin(): ?string { return $this->noteAdmin; }
    public function setNoteAdmin(?string $n): static { $this->noteAdmin = $n; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
    public function __toString(): string { return $this->titre ?? ''; }
}
