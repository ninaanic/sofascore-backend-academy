<?php

declare(strict_types=1);

namespace App\DTO;

class Country
{
    public function __construct(
        public int $id,
        public ?string $name,
    ) {
    }
}