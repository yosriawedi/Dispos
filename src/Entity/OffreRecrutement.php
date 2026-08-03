<?php

namespace App\Entity;

use App\Repository\OffreRecrutementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreRecrutementRepository::class)]
class OffreRecrutement
{
    public const TYPE_CDI        = 'CDI';
    public const TYPE_CDD        = 'CDD';
    public const TYPE_STAGE      = 'Stage';
    public const TYPE_FREELANCE  = 'Freelance';
    public const TYPE_ALTERNANCE = 'Alternance';

    public const STATUT_PUBLIEE  = 'publiee';
    public const STATUT_POURVUE  = 'pourvue';
    public const STATUT_EXPIREE  = 'expiree';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Entreprise qui dépose l'offre (ROLE_ENTREPRISE) */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $entreprise = null;

    #[ORM\Column(length: 255)]
    private ?string $poste = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    private string $typeContrat = self::TYPE_CDI;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $competences = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $localisation = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $teletravail = false;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $salaire = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateExpiration = null;

    #[ORM\Column(length: 20, options: ['default' => 'publiee'])]
    private string $statut = self::STATUT_PUBLIEE;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'offre', targetEntity: CandidatureRecrutement::class, cascade: ['remove'])]
    private Collection $candidatures;

    public function __construct()
    {
        $this->createdAt    = new \DateTimeImmutable();
        $this->candidatures = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getEntreprise(): ?User { return $this->entreprise; }
    public function setEntreprise(?User $u): static { $this->entreprise = $u; return $this; }
    public function getPoste(): ?string { return $this->poste; }
    public function setPoste(string $p): static { $this->poste = $p; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $d): static { $this->description = $d; return $this; }
    public function getTypeContrat(): string { return $this->typeContrat; }
    public function setTypeContrat(string $t): static { $this->typeContrat = $t; return $this; }
    public function getCompetences(): ?string { return $this->competences; }
    public function setCompetences(?string $c): static { $this->competences = $c; return $this; }
    public function getLocalisation(): ?string { return $this->localisation; }
    public function setLocalisation(?string $l): static { $this->localisation = $l; return $this; }
    public function isTeletravail(): bool { return $this->teletravail; }
    public function setTeletravail(bool $t): static { $this->teletravail = $t; return $this; }
    public function getSalaire(): ?string { return $this->salaire; }
    public function setSalaire(?string $s): static { $this->salaire = $s; return $this; }
    public function getDateExpiration(): ?\DateTimeImmutable { return $this->dateExpiration; }
    public function setDateExpiration(?\DateTimeImmutable $d): static { $this->dateExpiration = $d; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getCandidatures(): Collection { return $this->candidatures; }
    public function __toString(): string { return $this->poste ?? ''; }
}
