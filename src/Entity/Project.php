<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_CLOSED = 'closed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 20, options: ['default' => 'open'])]
    private string $status = self::STATUS_OPEN;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $neededSkills = null;

    #[ORM\Column(nullable: true)]
    private ?int $teamSize = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Startup $startup = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    public function getNeededSkills(): ?string { return $this->neededSkills; }
    public function setNeededSkills(?string $neededSkills): static { $this->neededSkills = $neededSkills; return $this; }
    public function getTeamSize(): ?int { return $this->teamSize; }
    public function setTeamSize(?int $teamSize): static { $this->teamSize = $teamSize; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getStartup(): ?Startup { return $this->startup; }
    public function setStartup(?Startup $startup): static { $this->startup = $startup; return $this; }
    public function __toString(): string { return $this->title ?? ''; }
}
