<?php

namespace App\Entity;

use App\Repository\IncubationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dossier d'incubation d'une entreprise (volet Pro TPE/PME).
 */
#[ORM\Entity(repositoryClass: IncubationRepository::class)]
class Incubation
{
    public const STATUT_EN_COURS  = 'en_cours';
    public const STATUT_TERMINEE  = 'terminee';
    public const STATUT_SUSPENDUE = 'suspendue';

    public const STADE_IDEE = 'idee';
    public const STADE_MVP  = 'mvp';
    public const STADE_LANCE = 'lance';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Entreprise porteuse du dossier — rôle attendu ROLE_ENTREPRISE, non contraint en base. */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $entreprise = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(length: 20, options: ['default' => 'en_cours'])]
    private string $statutGlobal = self::STATUT_EN_COURS;

    #[ORM\Column(length: 100)]
    private ?string $secteurActivite = null;

    #[ORM\Column(length: 20)]
    private string $stadeMaturite = self::STADE_IDEE;

    #[ORM\OneToMany(mappedBy: 'incubation', targetEntity: EtapeIncubation::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $etapes;

    #[ORM\OneToMany(mappedBy: 'incubation', targetEntity: DemandeConsultation::class, cascade: ['remove'])]
    private Collection $demandesConsultation;

    public function __construct()
    {
        $this->dateDebut = new \DateTimeImmutable();
        $this->etapes = new ArrayCollection();
        $this->demandesConsultation = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getEntreprise(): ?User { return $this->entreprise; }
    public function setEntreprise(?User $entreprise): static { $this->entreprise = $entreprise; return $this; }
    public function getDateDebut(): ?\DateTimeImmutable { return $this->dateDebut; }
    public function setDateDebut(\DateTimeImmutable $dateDebut): static { $this->dateDebut = $dateDebut; return $this; }
    public function getStatutGlobal(): string { return $this->statutGlobal; }
    public function setStatutGlobal(string $statutGlobal): static { $this->statutGlobal = $statutGlobal; return $this; }
    public function getSecteurActivite(): ?string { return $this->secteurActivite; }
    public function setSecteurActivite(string $secteurActivite): static { $this->secteurActivite = $secteurActivite; return $this; }
    public function getStadeMaturite(): string { return $this->stadeMaturite; }
    public function setStadeMaturite(string $stadeMaturite): static { $this->stadeMaturite = $stadeMaturite; return $this; }
    public function getEtapes(): Collection { return $this->etapes; }
    public function getDemandesConsultation(): Collection { return $this->demandesConsultation; }

    public function __toString(): string
    {
        return $this->entreprise?->getFullName() ?? ('Incubation #' . $this->id);
    }
}
