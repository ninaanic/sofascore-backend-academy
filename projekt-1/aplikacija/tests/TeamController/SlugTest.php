<?php

declare(strict_types=1);

namespace App\Tests\TeamController;

use App\Controller\TeamController;
use App\Tests\AssertTrait;
use App\Tests\KernelTestCase;
use SimpleFW\ORM\Connection;
use SimpleFW\ORM\QueryBuilder;

final class SlugTest extends KernelTestCase
{
    use AssertTrait;

    public function run(): void
    {
        $kernel = self::createKernel();
        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE team RESTART IDENTITY CASCADE');

        $queryBuilder = $kernel->getContainer()->get(QueryBuilder::class);

        $queryBuilder->insert('team', ['name' => 'NK Rudeš', 'slug' => 'nk-rudes', 'external_id' => '2de50c2a-b422-438f-aae7-3fcc54b82cb9', 'sport_id' => 1]);
        $queryBuilder->insert('team', ['name' => 'Utah Jazz', 'slug' => 'utah-jazz', 'external_id' => '3156017d-d948-42c1-b829-b4053c048eea', 'sport_id' => 2]);

        $homeController = $kernel->getContainer()->get(TeamController::class);

        $response = $homeController->slug('nk-rudes');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['name' => 'NK Rudeš', 'slug' => 'nk-rudes'],
        ], JSON_PRETTY_PRINT), $response->getContent());

        $response = $homeController->slug('utah-jazz');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['name' => 'Utah Jazz', 'slug' => 'utah-jazz'],
        ], JSON_PRETTY_PRINT), $response->getContent());
    }
}
