<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $filiere = null; // ex: Informatique, Maths, Physique

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $niveau = null; // Licence, Master, Doctorat

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $icone = null; // emoji

    #[ORM\Column(options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'matiere', targetEntity: SessionRevision::class, cascade: ['remove'])]
    private Collection $sessions;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->sessions  = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $n): static { $this->nom = $n; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }
    public function getFiliere(): ?string { return $this->filiere; }
    public function setFiliere(?string $f): static { $this->filiere = $f; return $this; }
    public function getNiveau(): ?string { return $this->niveau; }
    public function setNiveau(?string $n): static { $this->niveau = $n; return $this; }
    public function getIcone(): ?string { return $this->icone; }
    public function setIcone(?string $i): static { $this->icone = $i; return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $a): static { $this->active = $a; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getSessions(): Collection { return $this->sessions; }
    public function __toString(): string { return $this->nom ?? ''; }
}
