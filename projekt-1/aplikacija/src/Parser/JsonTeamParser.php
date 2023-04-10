<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Player;
use App\Tools\Slugger;
use SimpleFW\ORM\EntityManager;

final class JsonTeamParser
{
    public function __construct(
        private readonly Slugger $slugger,
        private readonly EntityManager $entityManager
    ) {
    }

    public function parse(string $json): Sport
    {
        $sportData = json_decode($json, true);
        
        $sport = new Sport(
            $sportData['sport']['name'],
            $this->slugger->slugify($sportData['sport']['name']),
            $sportData['sport']['id'],
        );
        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $teams = [];
        foreach ($sportData['teams'] as $teamData) {
            $teams[] = $this->createTeam($teamData, $sport);
        }
        $sport->setTeams($teams);
        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        return $sport;
    }

    private function createTeam(array $teamData, Sport $sport): Team
    {
        $team = new Team(
            $teamData['name'],
            $this->slugger->slugify($teamData['name']),
            $teamData['id'],
        );
        $team->setSportId($sport->getId());
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        $players = [];
        foreach ($teamData['players'] as $playerData) {
            $players[] = $this->createPlayer($playerData, $team);
        }
        $team->setPlayers($players);
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $team;
    }

    private function createPlayer(array $playerData, Team $team): Player
    {
        $player = new Player(
            $playerData['name'],
            $this->slugger->slugify($playerData['name']),
            $playerData['id'],
        );
        $player->setTeamId($team->getId());
        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $player;
    }
}
