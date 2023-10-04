<?php

declare(strict_types=1);

namespace App\DTO;

class Player
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $position,
        public ?string $dateOfBirth,
        public int $id,

        public Sport $sport,
        public Team $team,
        public Country $country
    ) {
    }
}