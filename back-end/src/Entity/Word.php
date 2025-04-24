<?php

namespace App\Entity;

use App\Repository\WordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WordRepository::class)]
#[ORM\Table(name: 'word')]
class Word
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: "word", length: 255)]
    private ?string $word = null;

    #[ORM\Column(name: "weight", type: 'float', precision: 10, scale: 5)]
    private ?float $weight = null;

    #[ORM\Column(name: "biph_count", type: 'smallint')]
    private ?int $biphCount = null;

    #[ORM\ManyToOne(targetEntity: Biph::class)]
    #[ORM\JoinColumn(name: "biph1_id", referencedColumnName: "id", nullable: false)]
    private ?Biph $biph1 = null;

    #[ORM\ManyToOne(targetEntity: Biph::class)]
    #[ORM\JoinColumn(name: "biph2_id", referencedColumnName: "id", nullable: false)]
    private ?Biph $biph2 = null;

    #[ORM\ManyToOne(targetEntity: Biph::class)]
    #[ORM\JoinColumn(name: "biph3_id", referencedColumnName: "id", nullable: true)]
    private ?Biph $biph3 = null;

    #[ORM\ManyToOne(targetEntity: Biph::class)]
    #[ORM\JoinColumn(name: "biph4_id", referencedColumnName: "id", nullable: true)]
    private ?Biph $biph4 = null;

    #[ORM\ManyToOne(targetEntity: Biph::class)]
    #[ORM\JoinColumn(name: "biph5_id", referencedColumnName: "id", nullable: true)]
    private ?Biph $biph5 = null;

    //#[ORM\Column(name: "stressed_biph", type: 'smallint')]
    //private ?int $stressedBiph = null;

    #[ORM\Column(name: "type", length: 255)]
    private ?string $type = null;

    #[ORM\Column(name: "type_word_code", type: 'integer')]
    private ?int $wordCode = null;

    // --- Getters and Setters ---

    public function getId(): ?int { return $this->id; }
    public function getWord(): ?string { return $this->word; }
    public function setWord(string $word): static { $this->word = $word; return $this; }

    public function getWeight(): ?float { return $this->weight; }
    public function setWeight(float $weight): static { $this->weight = $weight; return $this; }

    public function getBiphCount(): ?int { return $this->biphCount; }
    public function setBiphCount(int $biphCount): static { $this->biphCount = $biphCount; return $this; }

    public function getBiph1(): ?Biph { return $this->biph1; }
    public function setBiph1(Biph $biph1): static { $this->biph1 = $biph1; return $this; }

    public function getBiph2(): ?Biph { return $this->biph2; }
    public function setBiph2(Biph $biph2): static { $this->biph2 = $biph2; return $this; }

    public function getBiph3(): ?Biph { return $this->biph3; }
    public function setBiph3(Biph $biph3): static { $this->biph3 = $biph3; return $this; }

    public function getBiph4(): ?Biph { return $this->biph4; }
    public function setBiph4(?Biph $biph4): static { $this->biph4 = $biph4; return $this; }

    public function getBiph5(): ?Biph { return $this->biph5; }
    public function setBiph5(?Biph $biph5): static { $this->biph5 = $biph5; return $this; }

    public function getStressedBiph(): ?int { return $this->stressedBiph; }
    public function setStressedBiph(int $value): static { $this->stressedBiph = $value; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getWordCode(): ?int { return $this->wordCode; }
    public function setWordCode(int $wordCode): static { $this->wordCode = $wordCode; return $this; }

    // --- Classification methods ---
    public function isPositive(): bool { return $this->weight >= 0.15; }
    public function isNegative(): bool { return $this->weight <= -0.15; }
    public function isNeutral(): bool { return $this->weight > -0.15 && $this->weight < 0.15; }
}


