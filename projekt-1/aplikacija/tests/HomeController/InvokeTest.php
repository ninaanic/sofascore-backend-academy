<?php

declare(strict_types=1);

namespace App\Tests\HomeController;

use App\Controller\HomeController;
use App\Tests\AssertTrait;
use App\Tests\KernelTestCase;
use SimpleFW\ORM\Connection;
use SimpleFW\ORM\QueryBuilder;

final class InvokeTest extends KernelTestCase
{
    use AssertTrait;

    public function run(): void
    {
        $kernel = self::createKernel();
        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE sport RESTART IDENTITY CASCADE');

        $queryBuilder = $kernel->getContainer()->get(QueryBuilder::class);

        $queryBuilder->insert('sport', ['name' => 'Football', 'slug' => 'football', 'external_id' => '791b5011-6978-420d-9762-cc5a4ca14470']);
        $queryBuilder->insert('sport', ['name' => 'Basketball', 'slug' => 'basketball', 'external_id' => '377d37e0-5347-4992-a38e-f580c462361f']);

        $homeController = $kernel->getContainer()->get(HomeController::class);

        $response = $homeController->__invoke();

        $this->assert(200, $response->getStatusCode());
        $this->assert(['content-type' => 'application/json'], $response->getHeaders());
        $this->assert(json_encode([
            ['id' => 1, 'name' => 'Football', 'slug' => 'football', 'externalId' => '791b5011-6978-420d-9762-cc5a4ca14470'],
            ['id' => 2, 'name' => 'Basketball', 'slug' => 'basketball', 'externalId' => '377d37e0-5347-4992-a38e-f580c462361f'],
        ]), $response->getContent());
    }
}
