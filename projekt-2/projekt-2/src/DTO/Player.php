<?php

declare(strict_types=1);

namespace App\DTO;

class Player
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $position,
        public ?string $date_of_birth,
        public int $id,

        public Country $country
        // TODO logo
    ) {
    }
}