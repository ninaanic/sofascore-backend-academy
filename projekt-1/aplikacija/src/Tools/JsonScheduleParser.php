<?php

declare(strict_types=1);

namespace App\Tools;

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

        array_map(fn (array $tournament) => $this->createTournament($tournament), $sport['tournaments']);

        return new Sport(
            $sport['name'],
            $this->slugger->slugify($sport['name']),
            $sport['id']
        );
    }

    private function createTournament(array $tournament): Tournament
    {

        array_map(fn (array $event) => $this->createEvent($event), $tournament['events']);

        return new Tournament(
            $tournament['name'],
            $this->slugger->slugify($tournament['name']),
            $tournament['id']
        );
    }

    private function createEvent(array $event): Event
    {
        // review promijenit $event['id']
        $string_to_hash = $event['id'] . $event['home_team_id'] . $event['away_team_id'] . $event['start_date'];
        $slug = hash('sha256', $string_to_hash);
        return new Event(
            $slug,
            $event['status'], 
            $event['home_score'],
            $event['away_score'],
            new DateTimeImmutable($event['start_date']),
            $event['id'],
            $event['home_team_id'],
            $event['away_team_id']
        );
    }
}