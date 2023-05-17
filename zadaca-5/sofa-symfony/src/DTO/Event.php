<?php

declare(strict_types=1);

namespace App\DTO;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Event
{
    public function __construct(
        #[SerializedName('Id')]
        public ?string $id,
        #[SerializedName('HomeTeamId')]
        public ?string $home_team_id,
        #[SerializedName('AwayTeamId')]
        public ?string $away_team_id,
        #[SerializedName('StartDate')]
        public ?\DateTimeImmutable $start_date,
        #[SerializedName('HomeScore')]
        public ?int $home_score,
        #[SerializedName('AwayScore')]
        public ?int $away_score,
    ) {
    }
}