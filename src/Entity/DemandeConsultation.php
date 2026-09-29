<?php

namespace App\Entity;

use App\Repository\DemandeConsultationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Demande d'une entreprise pour faire consulter/réviser son dossier d'incubation par l'équipe DisPos.
 */
#[ORM\Entity(repositoryClass: DemandeConsultationRepository::class)]
class DemandeConsultation
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_TRAITEE = 'traitee';
    public const STATUT_REFUSEE = 'refusee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'demandesConsultation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Incubation $incubation = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateDemande = null;

    #[ORM\Column(length: 20, options: ['default' => 'en_attente'])]
    private string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\Column(type: 'text')]
    private ?string $message = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reponseAdmin = null;

    public function __construct()
    {
        $this->dateDemande = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getIncubation(): ?Incubation { return $this->incubation; }
    public function setIncubation(?Incubation $incubation): static { $this->incubation = $incubation; return $this; }
    public function getDateDemande(): ?\DateTimeImmutable { return $this->dateDemande; }
    public function setDateDemande(\DateTimeImmutable $dateDemande): static { $this->dateDemande = $dateDemande; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }
    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $message): static { $this->message = $message; return $this; }
    public function getReponseAdmin(): ?string { return $this->reponseAdmin; }
    public function setReponseAdmin(?string $reponseAdmin): static { $this->reponseAdmin = $reponseAdmin; return $this; }

    public function __toString(): string
    {
        return 'Demande #' . ($this->id ?? '?');
    }
}
