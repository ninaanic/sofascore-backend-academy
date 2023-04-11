<?php

declare(strict_types=1);

namespace App\Tests\EventController;

use App\Controller\EventController;
use App\Tests\AssertTrait;
use App\Tests\KernelTestCase;
use SimpleFW\ORM\Connection;
use SimpleFW\ORM\QueryBuilder;

final class DateTournamentTest extends KernelTestCase
{
    use AssertTrait;

    public function run(): void
    {
        $kernel = self::createKernel();
        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE event RESTART IDENTITY CASCADE');

        $queryBuilder = $kernel->getContainer()->get(QueryBuilder::class);

        $queryBuilder->insert('event', ['slug' => 'a07cd475a337c1cd16e1e567f82d720b5d969a7238642ac14a5cfc73f1376a07', 'home_score' => 0, 'away_score' => 1, 'start_date' => '2003-12-14 16:00:00', 'external_id' => '996b9f2b-443f-45c1-b5ef-73d332d8a41c', 'home_team_id' => 3, 'away_team_id' => 4, 'status' => 'finished', 'tournament_id' => 1]);
        $queryBuilder->insert('event', ['slug' => 'f8016e80ae66924ada6536c50072c8a67666b06c80bbd1e63f102761ff1d23d4', 'home_score' => 92, 'away_score' => 97, 'start_date' => '2009-04-03 23:00:00', 'external_id' => '84322fda-726c-431e-9bff-edd94663edbc', 'home_team_id' => 5, 'away_team_id' => 6, 'status' => 'finished', 'tournament_id' => 2]);

        $controller = $kernel->getContainer()->get(EventController::class);

        $response = $controller->date_tournament('laliga', '2003-12-14 16:00:00');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['id' => 1, 'slug' => 'a07cd475a337c1cd16e1e567f82d720b5d969a7238642ac14a5cfc73f1376a07', 'homeScore' => 0, 'awayScore' => 1, 'startDate' => '2003-12-14 16:00:00', 'externalId' => '996b9f2b-443f-45c1-b5ef-73d332d8a41c', 'homeTeamId' => 3, 'awayTeamId' => 4, 'status' => 'finished', 'tournamentId' => 1],
        ], JSON_PRETTY_PRINT), $response->getContent());

        $response = $controller->date_tournament('nba', '2009-04-03 23:00:00');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['id' => 2, 'slug' => 'f8016e80ae66924ada6536c50072c8a67666b06c80bbd1e63f102761ff1d23d4', 'homeScore' => 92, 'awayScore' => 97, 'startDate' => '2009-04-03 23:00:00', 'externalId' => '84322fda-726c-431e-9bff-edd94663edbc', 'homeTeamId' => 5, 'awayTeamId' => 6, 'status' => 'finished', 'tournamentId' => 2],
        ], JSON_PRETTY_PRINT), $response->getContent());
    }
}
