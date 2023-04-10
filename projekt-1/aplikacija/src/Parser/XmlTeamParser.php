<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Player;
use App\Tools\Slugger;
use SimpleFW\ORM\EntityManager;

final class XmlTeamParser
{
    public function __construct(
        private readonly Slugger $slugger,
        private readonly EntityManager $entityManager
    ) {
    }

    public function parse(string $xml): Sport
    {
        $sportData = new \SimpleXMLElement($xml);

        $sport = new Sport(
            (string) $sportData['sportName'],
            $this->slugger->slugify((string)  $sportData['sportName'],),
            (string) $sportData['sportId']
        );
        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $teams = [];
        foreach ($sportData->Team as $teamData) {
            $teams[] = $this->createTeam($teamData, $sport->getId());
        }
        $sport->setTeams($teams);
        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        return $sport;
    }

    private function createTeam(\SimpleXMLElement $teamData, int $sportId): Team
    {
        $team = new Team(
            (string) $teamData->Name,
            $this->slugger->slugify((string) $teamData->Name),
            (string) $teamData['id'], 
        );
        $team->setSportId($sportId);
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        $players = [];
        foreach ($teamData->Players->Player as $playerData) {
            if (isset($playerData['id'])) {
                $players[] = $this->createPlayer($playerData, $team->getId());
            }
        }
        $team->setPlayers($players);
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $team;
    }

    private function createPlayer(\SimpleXMLElement $playerData, int $teamId): Player
    {
        $player = new Player(
            (string) $playerData->Name,
            $this->slugger->slugify((string) $playerData->Name),
            (string) $playerData['id']
        );

        $player->setTeamId($teamId);
        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $player;
    }
}