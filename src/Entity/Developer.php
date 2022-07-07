<?php

namespace App\Entity;

use App\Repository\DeveloperRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeveloperRepository::class)]
class Developer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'developers')]
    #[ORM\JoinColumn(nullable: false)]
    private Team $team;

    #[ORM\Column(type: 'string', length: 255)]
    private string $key;

    #[ORM\OneToMany(mappedBy: 'source', targetEntity: Assessment::class, orphanRemoval: true)]
    private $assessments;

    #[ORM\OneToMany(mappedBy: 'developer', targetEntity: Confidence::class, orphanRemoval: true)]
    private $confidences;

    public function __construct()
    {
        $this->assessments = new ArrayCollection();
        $this->confidences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return Collection<int, Assessment>
     */
    public function getAssessments(): Collection
    {
        return $this->assessments;
    }

    public function addAssessment(Assessment $assessment): self
    {
        if (!$this->assessments->contains($assessment)) {
            $this->assessments[] = $assessment;
            $assessment->setSource($this);
        }

        return $this;
    }

    public function removeAssessment(Assessment $assessment): self
    {
        if ($this->assessments->removeElement($assessment)) {
            // set the owning side to null (unless already changed)
            if ($assessment->getSource() === $this) {
                $assessment->setSource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Confidence>
     */
    public function getConfidences(): Collection
    {
        return $this->confidences;
    }

    public function addConfidence(Confidence $confidence): self
    {
        if (!$this->confidences->contains($confidence)) {
            $this->confidences[] = $confidence;
            $confidence->setDeveloper($this);
        }

        return $this;
    }

    public function removeConfidence(Confidence $confidence): self
    {
        if ($this->confidences->removeElement($confidence)) {
            // set the owning side to null (unless already changed)
            if ($confidence->getDeveloper() === $this) {
                $confidence->setDeveloper(null);
            }
        }

        return $this;
    }
}
