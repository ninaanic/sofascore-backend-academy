<?php

declare(strict_types=1);

namespace App\DTO;

class Standing
{
    public function __construct(
        public int $position,
        public int $matches,
        public int $wins,
        public int $looses,
        public int $draws,
        public int $scores_for,
        public int $scores_against,
        public int $points
    ) {
    }
}