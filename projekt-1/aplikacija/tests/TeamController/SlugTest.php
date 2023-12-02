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
        $queryBuilder->insert('team', ['name' => 'Real Zaragoza', 'slug' => 'real-zaragoza', 'external_id' => '299aa3ad-55f3-46eb-b713-bc88297aea14', 'sport_id' => 1]);
        $queryBuilder->insert('team', ['name' => 'Albacete Balompié', 'slug' => 'albacete-balompie', 'external_id' => '38622929-a799-4cee-ba47-bbb7209f92c1', 'sport_id' => 1]);
        $queryBuilder->insert('team', ['name' => 'Charlotte Hornets', 'slug' => 'charlotte-hornets', 'external_id' => 'f556694c-c22a-49a3-9043-e8e1c49ed2fb', 'sport_id' => 2]);
        $queryBuilder->insert('team', ['name' => 'Miami Heat', 'slug' => 'miami-heat', 'external_id' => '4c40404b-a0f6-42e4-b1ea-8a791f07cf1a', 'sport_id' => 2]);

        $controller = $kernel->getContainer()->get(TeamController::class);

        $response = $controller->slug('nk-rudes');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['id' => 1, 'name' => 'NK Rudeš', 'slug' => 'nk-rudes', 'externalId' => '2de50c2a-b422-438f-aae7-3fcc54b82cb9', 'sportId' => 1],
        ], JSON_PRETTY_PRINT), $response->getContent());

        $response = $controller->slug('utah-jazz');

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['id' => 2, 'name' => 'Utah Jazz', 'slug' => 'utah-jazz', 'externalId' => '3156017d-d948-42c1-b829-b4053c048eea', 'sportId' => 2],
        ], JSON_PRETTY_PRINT), $response->getContent());
    }
}
