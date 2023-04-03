<?php

declare(strict_types=1);

namespace App\Parser;

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

        return new Sport (
            (string) $schedule['sportName'],
            $this->slugger->slugify((string)  $schedule['sportName'],),
            (string) $schedule['sportId'], 
            $tournaments, 
            []
        );
    }

    private function createTournament(\SimpleXMLElement $tournament): Tournament
    {
        $events = [];
        foreach ($tournament->Events->Event as $event) {
            $events[] = $this->createEvent($event);
        }

        //var_dump($events);

        return new Tournament (
            (string) $tournament->Name,
            $this->slugger->slugify((string) $tournament->Name),
            (string) $tournament['id'], 
            $events
        );
    }

    private function createEvent(\SimpleXMLElement $event): Event
    {
        $status = isset($event->Status) ? (string) $event->Status : 'not-started';
        return new Event (
            (string) "",
            isset($event->HomeScore) ? (int) $event->HomeScore : null,
            isset($event->AwayScore) ? (int) $event->AwayScore : null,
            new DateTimeImmutable((string) $event->StartDate),
            (string) $event['id'],         
            (string) $event->HomeTeamId, 
            (string) $event->AwayTeamId, 
            $status
        );
    }
}