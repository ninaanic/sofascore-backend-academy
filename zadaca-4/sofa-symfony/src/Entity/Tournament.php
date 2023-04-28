<?php

declare(strict_types=1);

namespace App\Entity;

class Tournament
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $id,
        /** @var Event[] */
        public array $events,
    ) {
    }
}