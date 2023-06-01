<?php

declare(strict_types=1);

namespace App\DTO;

class Team
{
    public function __construct(
        public string $name,
        public ?string $manager_name,
        public ?string $venue,
        public int $id,

        /** @var Event[] */
        public array $events,

        /** @var Player[] */
        public array $players,

        /** @var Tournament[] */
        public array $tournaments

        //TODO logo
    ) {
    }
}