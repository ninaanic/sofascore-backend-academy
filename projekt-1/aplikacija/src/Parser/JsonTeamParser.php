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
        private readonly EntityManager $entityManager,
    ) {}

    public function parse(string $json): void
    {
        $sportData = json_decode($json, true);

        $sportExternalId = $sportData['sport']['id'];
        
        $sport = $this->entityManager->findOneBy(Sport::class, ['externalId' => $sportExternalId]);
        if (!$sport) {
            $sport = new Sport(
                $sportData['sport']['name'],
                $this->slugger->slugify($sportData['sport']['name']),
                $sportExternalId,
            );
        } else {
            $sport->setName($sportData['sport']['name']);
            $sport->setSlug($this->slugger->slugify($sportData['sport']['name']));
        }
        $this->entityManager->persist($sport);
        $this->entityManager->flush();
        
        foreach ($sportData['teams'] as $teamData) {
            $teamExternalId = $teamData['id'];
            $team = $this->entityManager->findOneBy(Team::class, ['externalId' => $teamExternalId]);
            if (!$team) {
                $team = new Team(
                    $teamData['name'],
                    $this->slugger->slugify($teamData['name']),
                    $teamExternalId,
                );
                $team->setSportId($sport->getId());
            } else {
                $team->setName($teamData['name']);
                $team->setSlug($this->slugger->slugify($teamData['name']));
            }
            $this->entityManager->persist($team);
            $this->entityManager->flush();
            
            foreach ($teamData['players'] as $playerData) {
                $playerExternalId = $playerData['id'];
                $player = $this->entityManager->findOneBy(Player::class, ['externalId' => $playerExternalId]);
                if (!$player) {
                    $player = new Player(
                        $playerData['name'],
                        $this->slugger->slugify($playerData['name']),
                        $playerExternalId,
                    );
                    $player->setTeamId($team->getId());
                } else {
                    $player->setName($playerData['name']);
                    $player->setSlug($this->slugger->slugify($playerData['name']));
                }
                $this->entityManager->persist($player);
                $this->entityManager->flush();
            }
        }
    }
}
