<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Event;
use App\Entity\Sport;
use App\Entity\Tournament;
use App\Tools\Slugger;

final class XmlFeedParser
{
    public function __construct(
        private readonly Slugger $slugger,
    ) {
    }

    public function parse(string $xml): Sport
    {
        $sport = new \SimpleXMLElement($xml);

        $tournaments = [];
        foreach ($sport->Tournaments as $tournament) {
            $tournaments[] = $this->createTournament($tournament);
        }

        return new Sport(
            (string) $sport->Name,
            $this->slugger->slugify((string) $sport->Name),
            (string) $sport->Id,
            $tournaments,
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
            (string) $tournament->Id,
            $events,
        );
    }

    private function createEvent(\SimpleXMLElement $event): Event
    {
        return new Event(
            (string) $event->Id,
            (string) $event->HomeTeamId,
            (string) $event->AwayTeamId,
            new \DateTimeImmutable((string) $event->StartDate),
            isset($event->HomeScore) ? (int) $event->HomeScore : null,
            isset($event->AwayScore) ? (int) $event->AwayScore : null,
        );
    }
}
