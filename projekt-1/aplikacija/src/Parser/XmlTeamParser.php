<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Player;
use App\Tools\Slugger;
use SimpleFW\ORM\EntityManager;
use SimpleXMLElement;

final class XmlTeamParser
{
    public function __construct(
        private readonly Slugger $slugger,
        private readonly EntityManager $entityManager,
    ) {}

    public function parse(string $xml): void
    {
        $sportData = new SimpleXMLElement($xml);

        $sportExternalId = (string) $sportData['sportId'];

        $sport = $this->entityManager->findOneBy(Sport::class, ['externalId' => $sportExternalId]);
        if (!$sport) {
            $sport = new Sport(
                (string) $sportData['sportName'],
                $this->slugger->slugify((string) $sportData['sportName']),
                $sportExternalId,
            );
        } else {
            $sport->setName((string) $sportData['sportName']);
            $sport->setSlug($this->slugger->slugify((string) $sportData['sportName']));
        }
        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        foreach ($sportData->Team as $teamData) {
            $teamExternalId = (string) $teamData['id'];
            $team = $this->entityManager->findOneBy(Team::class, ['externalId' => $teamExternalId]);
            if (!$team) {
                $team = new Team(
                    (string) $teamData->Name,
                    $this->slugger->slugify((string) $teamData->Name),
                    $teamExternalId,
                );
                $team->setSportId($sport->getId());
            } else {
                $team->setName((string) $teamData->Name);
                $team->setSlug($this->slugger->slugify((string) $teamData->Name));
            }
            $this->entityManager->persist($team);
            $this->entityManager->flush();

            foreach ($teamData->Players->Player as $playerData) {
                if (isset($playerData['id'])) {
                    $player = $this->entityManager->findOneBy(Player::class, ['externalId' => $playerData['id']]);
                    if (!$player) {
                        $player = new Player(
                            (string) $playerData->Name,
                            $this->slugger->slugify((string) $playerData->Name),
                            (string) $playerData['id'],
                        );
                        $player->setTeamId($team->getId());
                    } else {
                        $player->setName((string) $playerData->Name);
                        $player->setSlug($this->slugger->slugify((string) $playerData->Name));
                    }
                    $this->entityManager->persist($player);
                    $this->entityManager->flush();
                }
            }
        }
    }
}
