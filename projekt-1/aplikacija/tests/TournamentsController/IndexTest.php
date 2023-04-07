<?php

declare(strict_types=1);

namespace App\Tests\TournamentsController;

use App\Controller\TournamentsController;
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
        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE tournament RESTART IDENTITY CASCADE');

        $queryBuilder = $kernel->getContainer()->get(QueryBuilder::class);

        $queryBuilder->insert('tournament', ['name' => 'Premier League', 'slug' => 'premier-league', 'external_id' => '0a3c8861-895b-4c19-8d12-2dd61fffa473', 'sport_id' => 1]);
        $queryBuilder->insert('tournament', ['name' => 'LaLiga', 'slug' => 'laliga', 'external_id' => '986813d8-1096-4e2e-963f-0d2348997bbc', 'sport_id' => 1]);
        $queryBuilder->insert('tournament', ['name' => 'NBA', 'slug' => 'nba', 'external_id' => '5c51df6a-a56f-4f93-bca5-523e184d97ee', 'sport_id' => 2]);

        $controller = $kernel->getContainer()->get(TournamentsController::class);

        $response = $controller->index('football');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['id' => 1, 'name' => 'Premier League'],
            ['id' => 2, 'name' => 'LaLiga'],
        ], JSON_PRETTY_PRINT), $response->getContent());

        $response = $controller->index('basketball');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['id' => 3, 'name' => 'NBA'],
        ], JSON_PRETTY_PRINT), $response->getContent());
    }
}
