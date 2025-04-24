<?php

namespace App\Entity;

use App\Repository\ScreenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScreenRepository::class)]
#[ORM\Table(name: 'screen')]
class Screen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // Свързваме се към Word за negative_word_id
    #[ORM\ManyToOne(targetEntity: Word::class)]
    #[ORM\JoinColumn(name: "negative_word_id", referencedColumnName: "id", nullable: true)]
    private ?Word $negativeWord = null;

    // Свързваме се към Word за positive_word_id
    #[ORM\ManyToOne(targetEntity: Word::class)]
    #[ORM\JoinColumn(name: "positive_word_id", referencedColumnName: "id", nullable: true)]
    private ?Word $positiveWord = null;

    // Свързваме се към Word за chosen_word_id
    #[ORM\ManyToOne(targetEntity: Word::class)]
    #[ORM\JoinColumn(name: "chosen_word_id", referencedColumnName: "id", nullable: true)]
    private ?Word $chosenWord = null;

    // Свързваме се към Image, ако има image_id (според диаграмата има връзка към image)
    #[ORM\ManyToOne(targetEntity: Image::class)]
    #[ORM\JoinColumn(name: "image_id", referencedColumnName: "id", nullable: true)]
    private ?Image $image = null;

    #[ORM\Column(name:"score", type: 'integer')]
    private int $score;

    public function __construct(?Image $image, ?Word $positiveWord, ?Word $negativeWord)
    {
        $this->image = $image;
        $this->positiveWord = $positiveWord;
        $this->negativeWord = $negativeWord;
        // Можеш да зададеш стойност по подразбиране на score или други полета, ако е нужно.
        $this->score = 0;  // Пример за задаване на стойност по подразбиране
    }

    // -- GETTERS / SETTERS --

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNegativeWord(): ?Word
    {
        return $this->negativeWord;
    }

    public function setNegativeWord(?Word $negativeWord): static
    {
        $this->negativeWord = $negativeWord;
        return $this;
    }

    public function getPositiveWord(): ?Word
    {
        return $this->positiveWord;
    }

    public function setPositiveWord(?Word $positiveWord): static
    {
        $this->positiveWord = $positiveWord;
        return $this;
    }

    public function getChosenWord(): ?Word
    {
        return $this->chosenWord;
    }

    public function setChosenWord(?Word $chosenWord): static
    {
        $this->chosenWord = $chosenWord;
        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;
        return $this;
    }
}