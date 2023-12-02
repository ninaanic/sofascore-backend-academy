<?php

declare(strict_types=1);

namespace App\Tests\PostController;

use App\Controller\PostController;
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

        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE post RESTART IDENTITY CASCADE');

        $queryBuilder = $kernel->getContainer()->get(QueryBuilder::class);

        $queryBuilder->insert('post', ['title' => 'Title1', 'text' => 'Text1', 'status' => 'published', 'date_created' => '2022-03-04 15:23:16']);
        $queryBuilder->insert('post', ['title' => 'Title2', 'text' => 'Text2', 'status' => 'draft', 'date_created' => '2023-02-01 10:13:27']);

        $postController = $kernel->getContainer()->get(PostController::class);

        $response = $postController->index();

        $this->assert(json_encode([
            ['id' => 1, 'title' => 'Title1', 'text' => 'Text1', 'status' => 'published', 'dateCreated' => '2022-03-04 15:23:16'],
            ['id' => 2, 'title' => 'Title2', 'text' => 'Text2', 'status' => 'draft', 'dateCreated' => '2023-02-01 10:13:27'],
        ]), $response->getContent());
        $this->assert(['Content-Type' => 'application/json'], $response->getHeaders());
    }
}
