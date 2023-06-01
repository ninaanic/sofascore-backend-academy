<?php

declare(strict_types=1);

namespace App\DTO;

class Score
{
    public function __construct(
        public int $id,
        public ?int $total,
        public ?int $period1,
        public ?int $period2,
        public ?int $period3,
        public ?int $period4,
        public ?int $overtime
    ) {
    }
}