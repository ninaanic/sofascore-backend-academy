<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Player;
use App\Tools\Slugger;

final class XmlTeamParser
{
    public function __construct(
        private readonly Slugger $slugger,
    ) {
    }

    public function parse(string $xml): Sport
    {
        $teams = new \SimpleXMLElement($xml);

        $teams_list = [];
        foreach ($teams->Team as $team) {
            $teams_list[] = $this->createTeam($team);
        }

        return new Sport(
            (string) $teams['sportName'],
            $this->slugger->slugify((string)  $teams['sportName'],),
            (string) $teams['sportId'], 
            [],
            $teams_list
        );
    }

    private function createTeam(\SimpleXMLElement $team): Team
    {
        $players = [];
        foreach ($team->Players as $player) {
            $players[] = $this->createPlayer($player);
        }

        return new Team(
            (string) $team->Name,
            $this->slugger->slugify((string) $team->Name),
            (string) $team['id'], 
            $players
        );
    }

    private function createPlayer(\SimpleXMLElement $player): Player
    {
        return new Player(
            (string) $player->Name,
            $this->slugger->slugify((string) $player->Name),
            (string) $player['id']
        );
    }
}