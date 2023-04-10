<?php

declare(strict_types=1);

namespace App\Entity;

use SimpleFW\ORM\Attribute\Column;
use SimpleFW\ORM\Attribute\Entity;
use SimpleFW\ORM\Attribute\Id;

#[Entity]
final class Standings implements \JsonSerializable
{
    #[Id]
    public int $id;
    #[Column]
    public int $position;
    #[Column]
    public int $matches;
    #[Column]
    public int $wins;
    #[Column]
    public int $looses;
    #[Column]
    public int $draws;
    #[Column(name: 'scores_for')]
    public int $scoresFor;
    #[Column(name: 'scores_against')]
    public int $scoresAgainst;
    #[Column]
    public int $points;


    #[Column(name: 'tournament_id')]
    public int $tournamentId;
    #[Column(name: 'team_id')]
    public int $teamId;

    public function __construct(int $position, int $matches, int $wins, int $looses, int $draws, int $scoresFor, int $scoresAgainst, int $points)
    {
        $this->position = $position;
        $this->matches = $matches;
        $this->wins = $wins;
        $this->looses = $looses;
        $this->draws = $draws;
        $this->scoresFor = $scoresFor;
        $this->scoresAgainst = $scoresAgainst;
        $this->points = $points;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getMatches(): int
    {
        return $this->matches;
    }

    public function setMatches(int $matches): self
    {
        $this->matches = $matches;

        return $this;
    }
    
    public function getWins(): int
    {
        return $this->wins;
    }

    public function setWins(int $wins): self
    {
        $this->wins = $wins;

        return $this;
    }

    public function getLooses(): int
    {
        return $this->looses;
    }

    public function setLooses(int $looses): self
    {
        $this->looses = $looses;

        return $this;
    }

    public function getDraws(): int
    {
        return $this->draws;
    }

    public function setDraws(int $draws): self
    {
        $this->draws = $draws;

        return $this;
    }
    
    public function getScoresFor(): int
    {
        return $this->scoresFor;
    }

    public function setScoresFor(int $scoresFor): self
    {
        $this->scoresFor = $scoresFor;

        return $this;
    }

    public function getScoresAgainst(): int
    {
        return $this->scoresAgainst;
    }

    public function setScoresAgainst(int $scoresAgainst): self
    {
        $this->scoresAgainst = $scoresAgainst;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

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

    public function getTeamId(): int
    {
        return $this->teamId;
    }

    public function setTeamId(int $teamId): self
    {
        $this->teamId = $teamId;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
