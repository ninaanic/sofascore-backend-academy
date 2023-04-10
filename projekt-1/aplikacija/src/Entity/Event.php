<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use SimpleFW\ORM\Attribute\Column;
use SimpleFW\ORM\Attribute\Entity;
use SimpleFW\ORM\Attribute\Id;

#[Entity]
final class Event implements \JsonSerializable
{
    #[Id]
    public int $id;
    #[Column]
    public string $slug;
    #[Column(name: 'home_score')]
    public ?int $homeScore;
    #[Column(name: 'away_score')]
    public ?int $awayScore;
    #[Column(name: 'start_date')]
    public string $startDate;
    #[Column(name: 'external_id')]
    public string $externalId;
    #[Column(name: 'home_team_id')]
    public int $homeTeamId;
    #[Column(name: 'away_team_id')]
    public int $awayTeamId;
    #[Column]
    public string $status;


    #[Column(name: 'tournament_id')]
    public int $tournamentId;

    public function __construct(string $slug, ?int $homeScore, ?int $awayScore, string $startDate,
                                string $externalId, int $homeTeamId, int $awayTeamId, string $status, int $tournamentId)
    {
        $this->slug = $slug;
       // $this->homeScore = isset($homeScore) ? $homeScore : null;
        $this->homeScore = $homeScore;
        $this->awayScore = isset($awayScore) ? $awayScore : null;
        $this->startDate = $startDate;
        $this->externalId = $externalId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
        $this->setStatus(EventStatusEnum::from($status) ?? EventStatusEnum::NotStarted);
        $this->tournamentId = $tournamentId;
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function setStartDate(string $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

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
        return $this->awayTeamId;
    }

    public function setAwayTeamId(int $awayTeamId): self
    {
        $this->awayTeamId = $awayTeamId;

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

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
