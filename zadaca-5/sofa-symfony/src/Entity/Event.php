<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $externalId = null;

    #[ORM\Column(length: 255)]
    private ?string $homeTeamExternalId = null;

    #[ORM\Column(length: 255)]
    private ?string $awayTeamExternalId = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $homeScore = null;

    #[ORM\Column(nullable: true)]
    private ?int $awayScore = null;

    #[ORM\Column]
    public ?int $tournamentId = null;

    #[ORM\Column]
    public ?int $homeTeamId = null;

    #[ORM\Column]
    public ?int $awayTeamId = null;

    public function __construct(string $externalId, string $homeTeamExternalId, string $awayTeamExternalId, 
                                \DateTimeInterface $startDate, ?int $homeScore, ?int $awayScore)
    {
        $this->homeScore = isset($homeScore) ? $homeScore : null;
        $this->awayScore = isset($awayScore) ? $awayScore : null;
        $this->startDate = $startDate;
        $this->externalId = $externalId;
        $this->homeTeamExternalId = $homeTeamExternalId;
        $this->awayTeamExternalId = $awayTeamExternalId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getHomeTeamExternalId(): ?string
    {
        return $this->homeTeamExternalId;
    }

    public function setHomeTeamExteranlId(string $homeTeamExternalId): self
    {
        $this->homeTeamExternalId = $homeTeamExternalId;

        return $this;
    }

    public function getAwayTeamExteranlId(): ?string
    {
        return $this->awayTeamExternalId;
    }

    public function setAwayTeamExteranlId(string $awayTeamExternalId): self
    {
        $this->awayTeamExternalId = $awayTeamExternalId;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->homeScore;
    }

    public function setHomeScore(?int $homeScore): self
    {
        $this->homeScore = $homeScore;

        return $this;
    }

    public function getAwayScore(): ?int
    {
        return $this->awayScore;
    }

    public function setAwayScore(?int $awayScore): self
    {
        $this->awayScore = $awayScore;

        return $this;
    }

    public function getTournamentId(): int
    {
        return $this->tournamentId;
    }

    public function setTournamentId(int $tournamentId): self
    {
        $this->tournamentId = $tournamentId;

        return $this;
    }

    public function getHomeTeamId(): int
    {
        return $this->homeTeamId;
    }

    public function setHomeTeamId(int $homeTeamId): self
    {
        $this->homeTeamId = $homeTeamId;

        return $this;
    }

    public function getAwayTeamId(): int
    {
        return $this->tournamentId;
    }

    public function setAwayTeamId(int $awayTeamId): self
    {
        $this->awayTeamId = $awayTeamId;

        return $this;
    }

}
