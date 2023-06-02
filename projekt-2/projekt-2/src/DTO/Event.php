<?php

declare(strict_types=1);

namespace App\DTO;

class Event
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $startDate,
        public string $status,
        public int $round,

        public Tournament $tournament,
        public Team $homeTeam,
        public Team $awayTeam,

        public array $homeScore,
        public array $awayScore,

        public ?string $winnerCode

        // TODO incidents
    ) {
    }
}