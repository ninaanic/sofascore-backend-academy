<?php
namespace App\Tools;

readonly class Tournament
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $id,
        // @var Event[] 
        public array  $events,
    ) {
    }
}
