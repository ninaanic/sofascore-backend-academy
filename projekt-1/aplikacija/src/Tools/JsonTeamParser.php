<?php

declare(strict_types=1);

namespace App\Tools;

use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Player;
use App\Tools\Slugger;

final class JsonTeamParser
{
    public function __construct(
        private readonly Slugger $slugger
    ) {
    }

    public function parse(string $json): Sport
    {
        $sport = json_decode($json, true);

        array_map(fn (array $team) => $this->createTeam($team), $sport['teams']);

        return new Sport(
            $sport['name'],
            $this->slugger->slugify($sport['name']),
            $sport['id']
        );
    }

    private function createTeam(array $team): Team
    {
        array_map(fn (array $player) => $this->createTeam($player), $team['players']);

        return new Team(
            $team['name'],
            $this->slugger->slugify($team['name']),
            $team['id']
        );
    }

    private function createPlayer(array $player): Player
    {
        return new Player(
            $player['name'],
            $this->slugger->slugify($player['name']),
            $player['id'],
        );
    }
}