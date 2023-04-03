<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Sport;
use App\Entity\Event;
use App\Entity\Tournament;
use App\Tools\Slugger;
use DateTimeImmutable;

final class JsonScheduleParser
{
    public function __construct(
        private readonly Slugger $slugger
    ) {
    }

    public function parse(string $json): Sport
    {
        $sport = json_decode($json, true);

        return new Sport (
            $sport['sport']['name'],
            $this->slugger->slugify($sport['sport']['name']),
            $sport['sport']['id'], 
            array_map(fn (array $tournament) => $this->createTournament($tournament), $sport['tournaments']), 
            []
        );
    }

    private function createTournament(array $tournament): Tournament
    {

        return new Tournament (
            $tournament['name'],
            $this->slugger->slugify($tournament['name']),
            $tournament['id'], 
            array_map(fn (array $event) => $this->createEvent($event, ), $tournament['events'])
        );
    }

    private function createEvent(array $event): Event
    {
        //$string_to_hash = $event . $event['home_team_id'] . $event['away_team_id'] . $event['start_date'];
        //$slug = hash('sha256', $string_to_hash);
        $status = isset($event['status']) ? (string) $event['status'] : 'not-started';
        return new Event(
            (string) "",
            isset($event['home_score']) ? (int) $event['home_score'] : null,
            isset($event['away_score']) ? (int) $event['away_score'] : null,
            new DateTimeImmutable((string) $event['start_date']),
            (string) $event['id'],         
            (string) $event['home_team_id'],
            (string) $event['away_team_id'],
            $status
        );
    }
}