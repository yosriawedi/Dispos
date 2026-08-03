<?php

namespace App\Entity;

use App\Repository\DemandeEncadrementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeEncadrementRepository::class)]
class DemandeEncadrement
{
    public const TYPE_PFE      = 'PFE';
    public const TYPE_PFA      = 'PFA';
    public const TYPE_DOCTORAT = 'Doctorat';

    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_ACCEPTEE   = 'acceptee';
    public const STATUT_REFUSEE    = 'refusee';
    public const STATUT_EN_COURS   = 'en_cours';
    public const STATUT_TERMINEE   = 'terminee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $etudiant = null;

    #[ORM\Column(length: 20)]
    private string $type = self::TYPE_PFE;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $stackTech = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etablissement = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $niveauEtude = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $encadreur = null;

    #[ORM\Column(length: 20, options: ['default' => 'en_attente'])]
    private string $statut = self::STATUT_EN_ATTENTE;

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
    public function getType(): string { return $this->type; }
    public function setType(string $t): static { $this->type = $t; return $this; }
    public function getSujet(): ?string { return $this->sujet; }
    public function setSujet(string $s): static { $this->sujet = $s; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }
    public function getStackTech(): ?string { return $this->stackTech; }
    public function setStackTech(?string $s): static { $this->stackTech = $s; return $this; }
    public function getEtablissement(): ?string { return $this->etablissement; }
    public function setEtablissement(?string $e): static { $this->etablissement = $e; return $this; }
    public function getNiveauEtude(): ?string { return $this->niveauEtude; }
    public function setNiveauEtude(?string $n): static { $this->niveauEtude = $n; return $this; }
    public function getEncadreur(): ?User { return $this->encadreur; }
    public function setEncadreur(?User $u): static { $this->encadreur = $u; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; $this->updatedAt = new \DateTimeImmutable(); return $this; }
    public function getNoteAdmin(): ?string { return $this->noteAdmin; }
    public function setNoteAdmin(?string $n): static { $this->noteAdmin = $n; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function __toString(): string { return sprintf('[%s] %s', $this->type, $this->sujet ?? ''); }
}
