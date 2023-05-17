<?php

declare(strict_types=1);

namespace App\DTO;

class Team
{
    public function __construct(
        public string $name,
        public string $id,
    ) {
    }
}