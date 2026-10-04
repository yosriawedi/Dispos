<?php

namespace App\Entity;

use App\Repository\SessionDocumentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Support de cours (PDF) déposé par l'équipe DisPos sur une session de
 * révision — visible dans l'espace de cours des étudiants inscrits.
 */
#[ORM\Entity(repositoryClass: SessionDocumentRepository::class)]
class SessionDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionRevision $session = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    private ?string $originalName = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getSession(): ?SessionRevision { return $this->session; }
    public function setSession(?SessionRevision $s): static { $this->session = $s; return $this; }
    public function getFilename(): ?string { return $this->filename; }
    public function setFilename(?string $f): static { $this->filename = $f; return $this; }
    public function getOriginalName(): ?string { return $this->originalName; }
    public function setOriginalName(?string $n): static { $this->originalName = $n; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function __toString(): string { return $this->originalName ?? ''; }
}
