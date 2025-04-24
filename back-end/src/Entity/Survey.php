<?php

namespace App\Entity;

use App\Repository\SurveyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: SurveyRepository::class)]
class Survey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50)]
    private ?string $userLanguage = null;

    #[Assert\PositiveOrZero]
    #[Assert\LessThanOrEqual(2)]
    #[ORM\Column(name:"user_gender", type: Types::SMALLINT, nullable: true)]
    private ?int $userGender = null;

    // RELATIONS WITH SCREEN TABLE (8 SCREENS)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "screen1_id", referencedColumnName: "id", nullable: false)]
    private ?Screen $screen1 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "screen2_id", referencedColumnName: "id", nullable: false)]
    private ?Screen $screen2 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "screen3_id", referencedColumnName: "id", nullable: false)]
    private ?Screen $screen3 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "screen4_id", referencedColumnName: "id", nullable: false)]
    private ?Screen $screen4 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "screen5_id", referencedColumnName: "id", nullable: false)]
    private ?Screen $screen5 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "screen6_id", referencedColumnName: "id", nullable: false)]
    private ?Screen $screen6 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "screen7_id", referencedColumnName: "id", nullable: false)]
    private ?Screen $screen7 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "screen8_id", referencedColumnName: "id", nullable: false)]
    private ?Screen $screen8 = null;

    #[ORM\Column(name:"started_date", nullable: true)]
    private ?\DateTimeImmutable $startedDate = null;

    #[ORM\Column(name:"finished_date", nullable: true)]
    private ?\DateTimeImmutable $finishedDate = null;

    #[ORM\Column(name:"score", type: 'float', precision: 10, scale: 5, nullable: true)]
    private ?float $score = null;

    // ---------------------------------------------------------
    // GETTERS / SETTERS
    // ---------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserLanguage(): ?string
    {
        return $this->userLanguage;
    }

    public function setUserLanguage(string $userLanguage): static
    {
        $this->userLanguage = $userLanguage;
        return $this;
    }

    public function getUserGender(): ?int
    {
        return $this->userGender;
    }

    public function setUserGender(?int $userGender): static
    {
        $this->userGender = $userGender;
        return $this;
    }

    public function getUserAge(): ?int
    {
        return $this->userAge;
    }

    public function setUserAge(?int $userAge): static
    {
        $this->userAge = $userAge;
        return $this;
    }

    // GETTERS AND SETTERS FOR SCREEN RELATIONS
    public function getScreen1(): ?Screen { return $this->screen1; }
    public function setScreen1(?Screen $screen1): static { $this->screen1 = $screen1; return $this; }

    public function getScreen2(): ?Screen { return $this->screen2; }
    public function setScreen2(?Screen $screen2): static { $this->screen2 = $screen2; return $this; }

    public function getScreen3(): ?Screen { return $this->screen3; }
    public function setScreen3(?Screen $screen3): static { $this->screen3 = $screen3; return $this; }

    public function getScreen4(): ?Screen { return $this->screen4; }
    public function setScreen4(?Screen $screen4): static { $this->screen4 = $screen4; return $this; }

    public function getScreen5(): ?Screen { return $this->screen5; }
    public function setScreen5(?Screen $screen5): static { $this->screen5 = $screen5; return $this; }

    public function getScreen6(): ?Screen { return $this->screen6; }
    public function setScreen6(?Screen $screen6): static { $this->screen6 = $screen6; return $this; }

    public function getScreen7(): ?Screen { return $this->screen7; }
    public function setScreen7(?Screen $screen7): static { $this->screen7 = $screen7; return $this; }

    public function getScreen8(): ?Screen { return $this->screen8; }
    public function setScreen8(?Screen $screen8): static { $this->screen8 = $screen8; return $this; }

    public function getStartedDate(): ?\DateTimeImmutable
    {
        return $this->startedDate;
    }

    #[ORM\PrePersist]
    public function setStartedDate(): static
    {
        $this->startedDate = new DateTimeImmutable('now');
        return $this;
    }

    public function getFinishedDate(): ?\DateTimeImmutable
    {
        return $this->finishedDate;
    }

    #[ORM\PreUpdate]
    public function setFinishedDate(): static
    {
        $this->finishedDate = new DateTimeImmutable('now');
        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): static
    {
        $this->score = $score;
        return $this;
    }

    public function getScreens(): array
    {
        return [
            $this->screen1,
            $this->screen2,
            $this->screen3,
            $this->screen4,
            $this->screen5,
            $this->screen6,
            $this->screen7,
            $this->screen8,
        ];
    }
}
