<?php

namespace App\Entity;

use App\Repository\CandidatureFormateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatureFormateurRepository::class)]
class CandidatureFormateur
{
    public const STATUT_SOUMISE   = 'soumise';
    public const STATUT_EN_REVIEW = 'en_review';
    public const STATUT_VALIDEE   = 'validee';
    public const STATUT_REFUSEE   = 'refusee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $candidat = null;

    #[ORM\ManyToMany(targetEntity: Matiere::class)]
    private Collection $matieres;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $stacks = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $experience = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $diplomes = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cvFilename = null;

    #[ORM\Column(nullable: true)]
    private ?int $disponibiliteHeures = null; // heures/semaine

    #[ORM\Column(nullable: true)]
    private ?float $tarifHoraire = null; // TND, nullable = négociable

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
        $this->matieres = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getCandidat(): ?User { return $this->candidat; }
    public function setCandidat(?User $u): static { $this->candidat = $u; return $this; }
    public function getMatieres(): Collection { return $this->matieres; }
    public function addMatiere(Matiere $matiere): static { if (!$this->matieres->contains($matiere)) { $this->matieres->add($matiere); } return $this; }
    public function removeMatiere(Matiere $matiere): static { $this->matieres->removeElement($matiere); return $this; }
    public function getStacks(): ?string { return $this->stacks; }
    public function setStacks(?string $s): static { $this->stacks = $s; return $this; }
    public function getExperience(): ?string { return $this->experience; }
    public function setExperience(?string $e): static { $this->experience = $e; return $this; }
    public function getDiplomes(): ?string { return $this->diplomes; }
    public function setDiplomes(?string $d): static { $this->diplomes = $d; return $this; }
    public function getCvFilename(): ?string { return $this->cvFilename; }
    public function setCvFilename(?string $c): static { $this->cvFilename = $c; return $this; }
    public function getDisponibiliteHeures(): ?int { return $this->disponibiliteHeures; }
    public function setDisponibiliteHeures(?int $h): static { $this->disponibiliteHeures = $h; return $this; }
    public function getTarifHoraire(): ?float { return $this->tarifHoraire; }
    public function setTarifHoraire(?float $t): static { $this->tarifHoraire = $t; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; $this->updatedAt = new \DateTimeImmutable(); return $this; }
    public function getNoteAdmin(): ?string { return $this->noteAdmin; }
    public function setNoteAdmin(?string $n): static { $this->noteAdmin = $n; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
    public function __toString(): string { return $this->candidat?->getFullName() ?? ''; }
}
