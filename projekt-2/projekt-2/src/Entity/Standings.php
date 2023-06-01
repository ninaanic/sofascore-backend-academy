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

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column]
    private ?int $matches = null;

    #[ORM\Column]
    private ?int $wins = null;

    #[ORM\Column]
    private ?int $looses = null;

    #[ORM\Column]
    private ?int $draws = null;

    #[ORM\Column]
    private ?int $scores_for = null;

    #[ORM\Column]
    private ?int $scores_against = null;

    #[ORM\Column]
    private ?int $points = null;

    #[ORM\Column]
    private ?int $tournament_id = null;

    #[ORM\Column]
    private ?int $team_id = null;

    public function __construct(?int $position, ?int $matches, ?int $wins,  ?int $looses, ?int $draws, ?int $scores_for, ?int $scores_against, ?int $points)
    {
        $this->position = $position;
        $this->matches = $matches;
        $this->wins = $wins;
        $this->looses = $looses;
        $this->draws = $draws;
        $this->scores_for = $scores_for;
        $this->scores_against = $scores_against;
        $this->points = $points;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getMatches(): ?int
    {
        return $this->matches;
    }

    public function setMatches(int $matches): self
    {
        $this->matches = $matches;

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
        return $this->looses;
    }

    public function setLooses(int $looses): self
    {
        $this->looses = $looses;

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

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

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
