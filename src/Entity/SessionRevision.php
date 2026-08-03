<?php

namespace App\Entity;

use App\Repository\SessionRevisionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRevisionRepository::class)]
class SessionRevision
{
    public const STATUT_PLANIFIEE = 'planifiee';
    public const STATUT_EN_COURS  = 'en_cours';
    public const STATUT_TERMINEE  = 'terminee';
    public const STATUT_ANNULEE   = 'annulee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $formateur = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateFin = null;

    #[ORM\Column(nullable: true)]
    private ?int $placesMax = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieu = null; // lien visio ou adresse

    #[ORM\Column(length: 20, options: ['default' => 'planifiee'])]
    private string $statut = self::STATUT_PLANIFIEE;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: InscriptionSession::class, cascade: ['remove'])]
    private Collection $inscriptions;

    public function __construct()
    {
        $this->createdAt    = new \DateTimeImmutable();
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $t): static { $this->titre = $t; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }
    public function getMatiere(): ?Matiere { return $this->matiere; }
    public function setMatiere(?Matiere $m): static { $this->matiere = $m; return $this; }
    public function getFormateur(): ?User { return $this->formateur; }
    public function setFormateur(?User $f): static { $this->formateur = $f; return $this; }
    public function getDateDebut(): ?\DateTimeImmutable { return $this->dateDebut; }
    public function setDateDebut(\DateTimeImmutable $d): static { $this->dateDebut = $d; return $this; }
    public function getDateFin(): ?\DateTimeImmutable { return $this->dateFin; }
    public function setDateFin(?\DateTimeImmutable $d): static { $this->dateFin = $d; return $this; }
    public function getPlacesMax(): ?int { return $this->placesMax; }
    public function setPlacesMax(?int $p): static { $this->placesMax = $p; return $this; }
    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(?string $l): static { $this->lieu = $l; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getInscriptions(): Collection { return $this->inscriptions; }

    public function getPlacesRestantes(): ?int
    {
        if ($this->placesMax === null) return null;
        return max(0, $this->placesMax - $this->inscriptions->count());
    }

    public function __toString(): string { return $this->titre ?? ''; }
}
