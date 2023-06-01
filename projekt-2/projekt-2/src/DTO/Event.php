<?php

declare(strict_types=1);

namespace App\DTO;

class Event
{
    public function __construct(
        public string $slug,
        public string $start_date,
        public string $status,
        public ?string $winner_code,
        public int $round,
        public int $id,

        /** @var Score[] */
        public array $home_score,

        /** @var Score[] */
        public array $away_score

        // TODO incidents
    ) {
    }
}