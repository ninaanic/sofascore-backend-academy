<?php

declare(strict_types=1);

namespace App\DTO;

class Tournament
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?int $number_of_competitors,
        public ?int $head_to_head_count,
        public int $id,

        /** @var Event[] */
        public array $events,

        /** @var Standing[] */
        public array $standings

        // TODO logo
    ) {
    }
}