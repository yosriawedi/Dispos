<?php

namespace App\Entity;

use App\Repository\StartupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StartupRepository::class)]
class Startup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $sector = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 20, options: ['default' => 'seed'])]
    private string $stage = 'seed';  // seed, series_a, series_b, growth

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $foundedAt = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'startups')]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'startup', targetEntity: Project::class, cascade: ['remove'])]
    private Collection $projects;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->tags = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }
    public function getSector(): ?string { return $this->sector; }
    public function setSector(string $sector): static { $this->sector = $sector; return $this; }
    public function getLogo(): ?string { return $this->logo; }
    public function setLogo(?string $logo): static { $this->logo = $logo; return $this; }
    public function getWebsite(): ?string { return $this->website; }
    public function setWebsite(?string $website): static { $this->website = $website; return $this; }
    public function getCountry(): ?string { return $this->country; }
    public function setCountry(?string $country): static { $this->country = $country; return $this; }
    public function getStage(): string { return $this->stage; }
    public function setStage(string $stage): static { $this->stage = $stage; return $this; }
    public function getFoundedAt(): ?\DateTimeImmutable { return $this->foundedAt; }
    public function setFoundedAt(?\DateTimeImmutable $foundedAt): static { $this->foundedAt = $foundedAt; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(User $owner): static { $this->owner = $owner; return $this; }

    public function getTags(): Collection { return $this->tags; }
    public function addTag(Tag $tag): static { if (!$this->tags->contains($tag)) { $this->tags->add($tag); } return $this; }
    public function removeTag(Tag $tag): static { $this->tags->removeElement($tag); return $this; }

    public function getProjects(): Collection { return $this->projects; }

    public function __toString(): string { return $this->name ?? ''; }
}
