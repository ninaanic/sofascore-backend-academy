<?php
namespace App\Tools;

readonly class Sport
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $id,
        // @var Tournament[] 
        public array  $tournaments,
    ) {
    }
}