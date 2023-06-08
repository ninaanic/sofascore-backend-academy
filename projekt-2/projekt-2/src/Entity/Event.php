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
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $start_date = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $winner_code = null;

    #[ORM\Column]
    private ?int $round = null;

    #[ORM\Column]
    private ?int $external_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $home_score = null;

    #[ORM\Column(nullable: true)]
    private ?int $away_score = null;

    #[ORM\Column]
    private ?int $home_team_id = null;

    #[ORM\Column]
    private ?int $away_team_id = null;

    #[ORM\Column]
    private ?int $tournament_id = null;

    public function __construct(string $slug, string $start_date, string $status, ?string $winner_code, int $round, int $external_id)
    {
        $this->slug = $slug;
        $this->start_date = $start_date;
        $this->setStatus(EventStatusEnum::from($status) ?? EventStatusEnum::NotStarted);
        $this->winner_code = $winner_code;
        $this->round = $round;
        $this->external_id = $external_id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getStartDate(): ?string
    {
        return $this->start_date;
    }

    public function setStartDate(string $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getStatus(): EventStatusEnum
    {
        return EventStatusEnum::from($this->status);
    }

    public function setStatus(EventStatusEnum $status): self
    {
        $this->status = $status->value;

        return $this;
    }

    public function getWinnerCode(): ?string
    {
        return $this->winner_code;
    }

    public function setWinnerCode(?string $winner_code): self
    {
        $this->winner_code = $winner_code;

        return $this;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getExternalId(): ?int
    {
        return $this->external_id;
    }

    public function setExternalId(int $external_id): self
    {
        $this->external_id = $external_id;

        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->home_score;
    }

    public function setHomeScore(?int $home_score): self
    {
        $this->home_score = $home_score;

        return $this;
    }

    public function getAwayScore(): ?int
    {
        return $this->away_score;
    }

    public function setAwayScore(?int $away_score): self
    {
        $this->away_score = $away_score;

        return $this;
    }

    public function getHomeTeamId(): ?int
    {
        return $this->home_team_id;
    }

    public function setHomeTeamId(int $home_team_id): self
    {
        $this->home_team_id = $home_team_id;

        return $this;
    }

    public function getAwayTeamId(): ?int
    {
        return $this->away_team_id;
    }

    public function setAwayTeamId(int $away_team_id): self
    {
        $this->away_team_id = $away_team_id;

        return $this;
    }

    public function getTournamentId(): ?int
    {
        return $this->tournament_id;
    }

    public function setTournamentId(int $tournament_id): self
    {
        $this->tournament_id = $tournament_id;

        return $this;
    }
}
