<?php

namespace App\Entity;

use App\Repository\StandingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandingsRepository::class)]
class Standings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column (nullable: true)]
    private ?int $points = null;

    #[ORM\Column]
    private ?int $scores_for = null;

    #[ORM\Column]
    private ?int $scores_against = null;

    #[ORM\Column]
    private ?int $played = null;

    #[ORM\Column]
    private ?int $wins = null;

    #[ORM\Column]
    private ?int $draws = null;

    #[ORM\Column]
    private ?int $losses = null;

    #[ORM\Column]
    private ?float $percentage = null;

    #[ORM\Column (nullable: true)]
    private ?int $external_id = null;

    #[ORM\Column]
    private ?int $tournament_id = null;

    #[ORM\Column]
    private ?int $team_id = null;

    public function __construct(?int $scores_for, ?int $scores_against, ?int $played, ?int $wins, ?int $draws, ?int $losses, ?float $percentage, ?int $external_id)
    {
        $this->scores_for = $scores_for;
        $this->scores_against = $scores_against;
        $this->played = $played;
        $this->wins = $wins;
        $this->draws = $draws;
        $this->losses = $losses;
        $this->percentage = $percentage;
        $this->external_id = $external_id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getScoresFor(): ?int
    {
        return $this->scores_for;
    }

    public function setScoresFor(int $scores_for): self
    {
        $this->scores_for = $scores_for;

        return $this;
    }

    public function getScoresAgainst(): ?int
    {
        return $this->scores_against;
    }

    public function setScoresAgainst(int $scores_against): self
    {
        $this->scores_against = $scores_against;

        return $this;
    }

    public function getPlayed(): ?int
    {
        return $this->played;
    }

    public function setPlayed(int $played): self
    {
        $this->played = $played;

        return $this;
    }


    public function getWins(): ?int
    {
        return $this->wins;
    }

    public function setWins(int $wins): self
    {
        $this->wins = $wins;

        return $this;
    }

    public function getLooses(): ?int
    {
        return $this->losses;
    }

    public function setLooses(int $losses): self
    {
        $this->losses = $losses;

        return $this;
    }

    public function getDraws(): ?int
    {
        return $this->draws;
    }

    public function setDraws(int $draws): self
    {
        $this->draws = $draws;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(float $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getExternalId(): ?int
    {
        return $this->external_id;
    }

    public function setExternalId(?int $external_id): self
    {
        $this->external_id = $external_id;

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

    public function getTeamId(): ?int
    {
        return $this->team_id;
    }

    public function setTeamId(int $team_id): self
    {
        $this->team_id = $team_id;

        return $this;
    }
}
