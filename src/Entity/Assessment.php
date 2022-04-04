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
    private $id;

    #[ORM\ManyToOne(targetEntity: Developer::class, inversedBy: 'assessments')]
    #[ORM\JoinColumn(nullable: false)]
    private $source;

    #[ORM\ManyToOne(targetEntity: Developer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $target;

    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'assessments')]
    #[ORM\JoinColumn(nullable: false)]
    private $topic;

    #[ORM\Column(type: 'string', length: 255)]
    private $value;

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
