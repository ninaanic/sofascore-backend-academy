<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Event;
use App\Entity\EventStatusEnum;
use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Tools\Slugger;
use DateTimeImmutable;
use SimpleFW\ORM\EntityManager;

final class XmlScheduleParser
{
    public function __construct(
        private readonly Slugger $slugger,
        private readonly EntityManager $entityManager,
    ) {
    }

    public function parse(string $xml): void
    {
        $sportData = new \SimpleXMLElement($xml);

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

        foreach ($sportData->Tournament as $tournamentData) {
            $tournamentExternalId = (string) $tournamentData['id'];
            $tournament = $this->entityManager->findOneBy(Tournament::class, ['externalId' => $tournamentExternalId]);
            if (!$tournament) {
                $tournament = new Tournament(
                    (string) $tournamentData->Name,
                    $this->slugger->slugify((string) $tournamentData->Name),
                    $tournamentExternalId,
                );
                $tournament->setSportId($sport->getId());
            } else {
                $tournament->setName((string) $tournamentData->Name);
                $tournament->setSlug($this->slugger->slugify((string) $tournamentData->Name));
            }
            $this->entityManager->persist($tournament);
            $this->entityManager->flush();

            foreach ($tournamentData->Events->Event as $eventData) {
                $eventExternalId = $eventData['id'];
                $event = $this->entityManager->findOneBy(Event::class, ['externalId' => $eventExternalId]);
                $homeTeam = $this->entityManager->findOneBy(Team::class, ['externalId' => $eventData->HomeTeamId]);
                $awayTeam = $this->entityManager->findOneBy(Team::class, ['externalId' => $eventData->AwayTeamId]);

                if (!$event) {
                    if ($homeTeam !== null && $awayTeam !== null) {
                        $HomeTeamId = $homeTeam->getId();
                        $AwayTeamId = $awayTeam->getId();

                        $string_to_hash = $tournament->getId() . $HomeTeamId . $AwayTeamId . $eventData->StartDate;
                        $slug = hash('sha256', $string_to_hash);
                        $status = isset($eventData->Status) ? (string) $eventData->Status : 'not-started';

                        $event = new Event(
                            (string) $slug,
                            isset($eventData->HomeScore) ? (int) $eventData->HomeScore : null,
                            isset($eventData->AwayScore) ? (int) $eventData->AwayScore : null,
                            (string) $eventData->StartDate,
                            (string) $eventData['id'],
                            $HomeTeamId,
                            $AwayTeamId,
                            $status
                        );
                        $event->setTournamentId($tournament->getId());
                    }
                } else {
                    if ($homeTeam !== null && $awayTeam !== null) {
                        $HomeTeamId = $homeTeam->getId();
                        $AwayTeamId = $awayTeam->getId();

                        $string_to_hash = $tournament->getId() . $HomeTeamId . $AwayTeamId . $eventData->StartDate;
                        $slug = hash('sha256', $string_to_hash);
                        $status = isset($eventData->Status) ? (string) $eventData->Status : 'not-started';

                        $event->setSlug($slug);
                        $event->setHomeScore($eventData->HomeScore);
                        $event->setAwayScore($eventData->AwayScore);
                        $event->setStartDate($eventData->StartDate);
                        $event->setHomeTeamId($HomeTeamId);
                        $event->setAwayTeamId($AwayTeamId);
                        $event->setStatus(EventStatusEnum::from($status) ?? EventStatusEnum::NotStarted);
                    }
                }
                $this->entityManager->persist($event);
                $this->entityManager->flush();

            }
        }
    }
}