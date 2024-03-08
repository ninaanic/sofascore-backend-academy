<?php
namespace Sofa\Homework\Entities;

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
