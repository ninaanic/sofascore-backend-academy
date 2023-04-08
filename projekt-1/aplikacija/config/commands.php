<?php

declare(strict_types=1);

return [
    'app:example' => \App\Command\ExampleCommand::class,
    'app:parse:team' => \App\Command\ParseTeamCommand::class,
    'app:parse:schedule' => \App\Command\ParseScheduleCommand::class,
    'app:calculate-standings' => \App\Command\CalculateStandingsCommand::class,
];
