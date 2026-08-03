<?php

namespace App\Entity;

use App\Repository\ProjetInterneDisposRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Projet interne de DisPos : un étudiant peut y contribuer en échange
 * d'une réduction sur son incubation.
 */
#[ORM\Entity(repositoryClass: ProjetInterneDisposRepository::class)]
class ProjetInterneDispos
{
    public const STATUT_OUVERT   = 'ouvert';
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_TERMINE  = 'termine';
    public const STATUT_ARCHIVE  = 'archive';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    /** Domaine : Marketing, Dev, Design, Étude de marché… */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $domaine = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $competencesRequises = null;

    #[ORM\Column(nullable: true)]
    private ?int $placesMax = null;

    #[ORM\Column(length: 20, options: ['default' => 'ouvert'])]
    private string $statut = self::STATUT_OUVERT;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateLimite = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'projetInterne', targetEntity: ContributionProjetInterne::class, cascade: ['remove'])]
    private Collection $contributions;

    public function __construct()
    {
        $this->createdAt     = new \DateTimeImmutable();
        $this->contributions = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $t): static { $this->titre = $t; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $d): static { $this->description = $d; return $this; }
    public function getDomaine(): ?string { return $this->domaine; }
    public function setDomaine(?string $d): static { $this->domaine = $d; return $this; }
    public function getCompetencesRequises(): ?string { return $this->competencesRequises; }
    public function setCompetencesRequises(?string $c): static { $this->competencesRequises = $c; return $this; }
    public function getPlacesMax(): ?int { return $this->placesMax; }
    public function setPlacesMax(?int $p): static { $this->placesMax = $p; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; return $this; }
    public function getDateLimite(): ?\DateTimeImmutable { return $this->dateLimite; }
    public function setDateLimite(?\DateTimeImmutable $d): static { $this->dateLimite = $d; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getContributions(): Collection { return $this->contributions; }

    public function getPlacesRestantes(): ?int
    {
        if ($this->placesMax === null) return null;
        $actives = $this->contributions->filter(
            fn(ContributionProjetInterne $c) => $c->getStatut() !== ContributionProjetInterne::STATUT_REFUSEE
        )->count();
        return max(0, $this->placesMax - $actives);
    }

    public function __toString(): string { return $this->titre ?? ''; }
}
