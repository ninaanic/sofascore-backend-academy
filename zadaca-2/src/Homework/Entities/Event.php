<?php
namespace Sofa\Homework\Entities;
use DateTimeImmutable;
readonly class Event
{
    public function __construct(
        public string $id,
        public string $home_team_id,
        public string $away_team_id,
        public DateTimeImmutable $start_date,
        public ?int $home_score,
        public ?int $away_score
    ) {
    }
}