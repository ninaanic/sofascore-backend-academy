<?php

declare(strict_types=1);

namespace App\Tests\PlayerController;

use App\Controller\PlayerController;
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
        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE player RESTART IDENTITY CASCADE');

        $queryBuilder = $kernel->getContainer()->get(QueryBuilder::class);

        $queryBuilder->insert('player', ['name' => 'Toni Kovacevic', 'slug' => 'toni-kovacevic', 'external_id' => 'f4dcd694-c4da-4b01-b60e-fa27b7da25f2', 'team_id' => 1]);
        $queryBuilder->insert('player', ['name' => 'Dante Exum', 'slug' => 'dante-exum', 'external_id' => '0c387e97-dd47-45f4-831d-ca9a8a796c3d', 'team_id' => 2]);

        $homeController = $kernel->getContainer()->get(PlayerController::class);

        $response = $homeController->index('nk-rudes');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['id' => 1, 'name' => 'Toni Kovacevic'],
        ], JSON_PRETTY_PRINT), $response->getContent());

        $response = $homeController->index('utah-jazz');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['id' => 2, 'name' => 'Dante Exum'],
        ], JSON_PRETTY_PRINT), $response->getContent());
    }
}
