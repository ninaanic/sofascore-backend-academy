<?php

declare(strict_types=1);

namespace App\Tests\StandingsController;

use App\Controller\StandingsController;
use App\Tests\AssertTrait;
use App\Tests\KernelTestCase;
use SimpleFW\ORM\Connection;
use SimpleFW\ORM\QueryBuilder;

final class IndexTest extends KernelTestCase
{
    use AssertTrait;

    public function run(): void
    {
        $kernel = self::createKernel();
        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE standings RESTART IDENTITY CASCADE');
        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE team RESTART IDENTITY CASCADE');
        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE tournament RESTART IDENTITY CASCADE');

        $queryBuilder = $kernel->getContainer()->get(QueryBuilder::class);

        $queryBuilder->insert('tournament', ['name' => 'HNL', 'slug' => 'hnl', 'external_id' => '7903d21e-599f-428d-bf8a-5a9bb4e3be5c', 'sport_id' => 1]);

        $queryBuilder->insert('team', ['name' => 'GNK Dinamo Zagreb', 'slug' => 'gnk-dinamo-zagreb', 'external_id' => 'a46fe41e-293b-4169-b6f7-3ff5313063e6', 'sport_id' => 1]);
        $queryBuilder->insert('team', ['name' => 'HNK Hajduk Split', 'slug' => 'hnk-hajduk-split', 'external_id' => 'a6865d6e-9167-424d-b956-e18afd120b43', 'sport_id' => 1]);

        $queryBuilder->insert('standings', ['position' => 1, 'matches' => 501, 'wins' => 357, 'looses' => 51, 'draws' => 93, 'scores_for' => 1049, 'scores_against' => 323, 'points' => 1164, 'tournament_id' => 1, 'team_id' => 1]);
        $queryBuilder->insert('standings', ['position' => 2, 'matches' => 502, 'wins' => 259, 'looses' => 124, 'draws' => 119, 'scores_for' => 834, 'scores_against' => 506, 'points' => 896, 'tournament_id' => 1, 'team_id' => 2]);

        $controller = $kernel->getContainer()->get(StandingsController::class);

        $response = $controller->index('hnl');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
                "tournament" => [
                    "id" => 1,
                    "name" => "HNL",
                    "slug" => "hnl"
                ],
                "rows" => [
                    [
                        "team" => [
                            [
                                "id" => 1,
                                "name" => "GNK Dinamo Zagreb",
                                "slug" => "gnk-dinamo-zagreb"
                            ]
                        ],
                        "id" => 1,
                        "position" => 1,
                        "matches" => 501,
                        "wins" => 357,
                        "looses" => 51,
                        "draws" => 93,
                        "scores_for" => 1049,
                        "scores_against" => 323,
                        "points" => 1164,
                        "tournament_id" => 1,
                        "team_id" => 1
                    ],
                    [
                        "team" => [
                            [
                                "id" => 2,
                                "name" => "HNK Hajduk Split",
                                "slug" => "hnk-hajduk-split"
                            ]
                        ],
                        "id" => 2,
                        "position" => 2,
                        "matches" => 502,
                        "wins" => 259,
                        "looses" => 124,
                        "draws" => 119,
                        "scores_for" => 834,
                        "scores_against" => 506,
                        "points" => 896,
                        "tournament_id" => 1,
                        "team_id" => 2
                    ]
                ]
            ], JSON_PRETTY_PRINT), $response->getContent());
    }
}
