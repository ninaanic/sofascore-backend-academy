<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\EventStatusEnum;
use App\Entity\Sport;
use App\Entity\Event;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Tools\Slugger;
use DateTimeImmutable;
use SimpleFW\ORM\EntityManager;

final class JsonScheduleParser
{
    public function __construct(
        private readonly Slugger $slugger, 
        private readonly EntityManager $entityManager,
    ) {
    }

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

        foreach ($sportData['tournaments'] as $tournamentData) {
            $tournamentExternalId = $tournamentData['id'];
            $tournament = $this->entityManager->findOneBy(Tournament::class, ['externalId' => $tournamentExternalId]);
            if (!$tournament) {
                $tournament = new Tournament(
                    $tournamentData['name'],
                    $this->slugger->slugify($tournamentData['name']),
                    $tournamentExternalId,
                );
                $tournament->setSportId($sport->getId());
            } else {
                $tournament->setName($tournamentData['name']);
                $tournament->setSlug($this->slugger->slugify($tournamentData['name']));
            }
            $this->entityManager->persist($tournament);
            $this->entityManager->flush();

            foreach ($tournamentData['events'] as $eventData) {
                $eventExternalId = $eventData['id'];
                $event = $this->entityManager->findOneBy(Event::class, ['externalId' => $eventExternalId]);
                $homeTeam = $this->entityManager->findOneBy(Team::class, ['externalId' => $eventData['home_team_id']]);
                $awayTeam = $this->entityManager->findOneBy(Team::class, ['externalId' => $eventData['away_team_id']]);

                if (!$event) {
                    if ($homeTeam !== null && $awayTeam !== null) {
                        $HomeTeamId = $homeTeam->getId();
                        $AwayTeamId = $awayTeam->getId();

                        $string_to_hash = $tournament->getId() . $HomeTeamId . $AwayTeamId . $eventData['start_date'];
                        $slug = hash('sha256', $string_to_hash);
                        $status = isset($eventData['status']) ? (string) $eventData['status'] : 'not-started';

                        $event = new Event(
                            (string) $slug,
                            isset($eventData['home_score']) ? (int) $eventData['home_score'] : null,
                            isset($eventData['away_score']) ? (int) $eventData['away_score'] : null,
                            (string) $eventData['start_date'],
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

                        $string_to_hash = $tournament->getId() . $HomeTeamId . $AwayTeamId . $eventData['start_date'];
                        $slug = hash('sha256', $string_to_hash);
                        $status = isset($eventData['status']) ? (string) $eventData['status'] : 'not-started';

                        $event->setSlug($slug);
                        $event->setHomeScore($eventData['home_score']);
                        $event->setAwayScore($eventData['away_score']);
                        $event->setStartDate($eventData['start_date']);
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