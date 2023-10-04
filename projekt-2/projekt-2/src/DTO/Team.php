<?php

declare(strict_types=1);

namespace App\DTO;

class Team
{
    public function __construct(
        public string $name,
        public ?string $managerName,
        public ?string $venue,
        public int $id,

        public Country $country,
        public ?array $tournaments,
        //TODO logo
    ) {
    }
}