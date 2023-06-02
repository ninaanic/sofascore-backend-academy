<?php

declare(strict_types=1);

namespace App\DTO;

class Tournament
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public Sport $sport,
        public Country $country,

        public ?int $number_of_competitors,
        public ?int $head_to_head_count

        // TODO logo
    ) {
    }
}