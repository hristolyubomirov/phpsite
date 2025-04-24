<?php

namespace App\Entity;

use App\Repository\BiphRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BiphRepository::class)]
class Biph
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name:"syllable",type:"string", length: 255)]
    private ?string $syllable = null;

    #[ORM\Column(name:"weight", type: "float", precision: 10, scale: 5)]
    private ?float $weight = null;

    #[ORM\Column(name:"p_value", type: "float", precision: 10, scale: 5)]
    private ?float $pValue = null;

    #[ORM\Column(name:"number",type:"string", length: 255)]
    private ?string $number = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSyllable(): ?string
    {
        return $this->syllable;
    }

    public function setSyllable(string $syllable): static
    {
        $this->syllable = $syllable;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getPValue(): ?float
    {
        return $this->pValue;
    }

    public function setPValue(float $pValue): static
    {
        $this->pValue = $pValue;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function isPositive(): bool
    {
        return $this->weight >= 0.15;
    }

    public function isNegative(): bool
    {
        return $this->weight <= -0.15;
    }

    public function isNeutral(): bool
    {
        return $this->weight > -0.15 && $this->weight < 0.15;
    }

    

}
