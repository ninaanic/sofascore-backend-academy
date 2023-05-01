<?php

declare(strict_types=1);

namespace App\Entity;

class Sport
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $id,
        /** @var Tournament[] */
        public array $tournaments,
    ) {
    }
}