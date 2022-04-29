<?php

namespace App\Entity;

use App\Repository\AssessmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssessmentRepository::class)]
class Assessment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Developer::class, inversedBy: 'assessments')]
    #[ORM\JoinColumn(nullable: false)]
    private Developer $source;

    #[ORM\ManyToOne(targetEntity: Developer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Developer $target;

    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'assessments')]
    #[ORM\JoinColumn(nullable: false)]
    private Topic $topic;

    #[ORM\Column(type: 'integer')]
    private int $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?Developer
    {
        return $this->source;
    }

    public function setSource(?Developer $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTarget(): ?Developer
    {
        return $this->target;
    }

    public function setTarget(?Developer $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
