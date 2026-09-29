<?php

namespace App\Entity;

use App\Repository\CandidatureRecrutementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatureRecrutementRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_candidature_recrutement', columns: ['candidat_id', 'offre_id'])]
class CandidatureRecrutement
{
    public const STATUT_SOUMISE     = 'soumise';
    public const STATUT_VUE         = 'vue';
    public const STATUT_SHORTLISTEE = 'shortlistee';
    public const STATUT_REFUSEE     = 'refusee';
    public const STATUT_ACCEPTEE    = 'acceptee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $candidat = null;

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OffreRecrutement $offre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $lettreMotivation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cvUrl = null;

    #[ORM\Column(length: 20, options: ['default' => 'soumise'])]
    private string $statut = self::STATUT_SOUMISE;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $messageEntreprise = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getCandidat(): ?User { return $this->candidat; }
    public function setCandidat(?User $u): static { $this->candidat = $u; return $this; }
    public function getOffre(): ?OffreRecrutement { return $this->offre; }
    public function setOffre(?OffreRecrutement $o): static { $this->offre = $o; return $this; }
    public function getLettreMotivation(): ?string { return $this->lettreMotivation; }
    public function setLettreMotivation(?string $l): static { $this->lettreMotivation = $l; return $this; }
    public function getCvUrl(): ?string { return $this->cvUrl; }
    public function setCvUrl(?string $c): static { $this->cvUrl = $c; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; $this->updatedAt = new \DateTimeImmutable(); return $this; }
    public function getMessageEntreprise(): ?string { return $this->messageEntreprise; }
    public function setMessageEntreprise(?string $m): static { $this->messageEntreprise = $m; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
