<?php

declare(strict_types=1);

namespace App\DTO;

class Sport
{
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $slug,
    ) {
    }
}