<?php

declare(strict_types=1);

namespace App\DTO;

class Standing
{
    public function __construct(
        public int $id, 
        
        public string $type,

        public ?Tournament $tournament,
        public ?array $sortedStandingsRows,
    ) {
    }
}