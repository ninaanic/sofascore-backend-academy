<?php

declare(strict_types=1);

namespace App\Tools;

use App\Entity\Event;
use App\Entity\EventStatusEnum;
use App\Entity\Sport;
use App\Entity\Tournament;
use App\Tools\Slugger;
use DateTimeImmutable;

final class XmlScheduleParser
{
    public function __construct(
        private readonly Slugger $slugger,
    ) {
    }

    public function parse(string $xml): Sport
    {
        $schedule = new \SimpleXMLElement($xml);

        $tournaments = [];
        foreach ($schedule->Tournament as $tournament) {
            $tournaments[] = $this->createTournament($tournament);
        }

        return new Sport(
            (string) $schedule['sportName'],
            $this->slugger->slugify((string)  $schedule['sportName'],),
            (string) $schedule['sportId']
        );
    }

    private function createTournament(\SimpleXMLElement $tournament): Tournament
    {
        $events = [];
        foreach ($tournament->Events as $event) {
            $events[] = $this->createEvent($event);
        }

        return new Tournament(
            (string) $tournament->Name,
            $this->slugger->slugify((string) $tournament->Name),
            (string) $tournament['id']
        );
    }

    private function createEvent(\SimpleXMLElement $event): Event
    {
        // review promijenit $event['id']
        $string_to_hash = $event['id'] . $event->HomeTeamId . $event->AwayTeamId . $event->StartDate;
        $slug = hash('sha256', $string_to_hash);
        return new Event(
            (string) $slug,
            $event->Status,
            isset($event->HomeScore) ? (int) $event->HomeScore : null,
            isset($event->AwayScore) ? (int) $event->AwayScore : null,
            new DateTimeImmutable((string) $event->StartDate),
            (string) $event['id'],
            (string) $event->HomeTeamId,
            (string) $event->AwayTeamId,
        );
    }
}