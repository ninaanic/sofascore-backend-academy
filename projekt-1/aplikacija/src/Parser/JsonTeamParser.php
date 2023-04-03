<?php

declare(strict_types=1);

namespace App\Parser;

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

        return new Sport (
            $sport['sport']['name'],
            $this->slugger->slugify($sport['sport']['name']),
            $sport['sport']['id'], 
            [],
            array_map(fn (array $team) => $this->createTeam($team), $sport['teams'])
        );
    }

    private function createTeam(array $team): Team
    {
        return new Team (
            $team['name'],
            $this->slugger->slugify($team['name']),
            $team['id'], 
            array_map(fn (array $player) => $this->createPlayer($player), $team['players'])
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