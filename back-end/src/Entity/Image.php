<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'image')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name:"filename", length: 255)]
    private ?string $filename = null;

    #[ORM\Column(name:"type", length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private ?string $type = null;

    #[ORM\Column(name:"is_positive", type: 'boolean')]
    private ?bool $isPositive = null;

    #[ORM\Column(name:"consensus", type: 'float', precision: 10, scale: 5)]
    private ?float $consensus = null;

    //#[ORM\Column(length: 50, nullable: true)]
    //private ?string $category = null;
    

    // -- GETTERS / SETTERS --

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getIsPositive(): bool
    {
        return (bool) $this->isPositive;
    }

    public function setIsPositive(bool $isPositive): static
    {
        $this->isPositive = $isPositive;
        return $this;
    }

    public function getConsensus(): ?float
    {
        return $this->consensus;
    }

    public function setConsensus(float $consensus): static
    {
        $this->consensus = $consensus;
        return $this;
    }
}
