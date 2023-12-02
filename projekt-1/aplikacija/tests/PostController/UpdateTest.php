<?php

declare(strict_types=1);

namespace App\Tests\PostController;

use App\Controller\PostController;
use App\Tests\AssertTrait;
use App\Tests\KernelTestCase;
use SimpleFW\HTTP\Request;
use SimpleFW\ORM\Connection;
use SimpleFW\ORM\QueryBuilder;

final class UpdateTest extends KernelTestCase
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

        $response1 = $postController->update(new Request(content: json_encode(['text' => 'New text1'])), 1);
        $this->assert(json_encode(['id' => 1, 'title' => 'Title1', 'text' => 'New text1', 'status' => 'published', 'dateCreated' => '2022-03-04 15:23:16']), $response1->getContent());
        $this->assert(['Content-Type' => 'application/json'], $response1->getHeaders());

        $response2 = $postController->update(new Request(content: json_encode(['title' => 'New title2', 'status' => 'published'])), 2);
        $this->assert(json_encode(['id' => 2, 'title' => 'New title2', 'text' => 'Text2', 'status' => 'published', 'dateCreated' => '2023-02-01 10:13:27']), $response2->getContent());
        $this->assert(['Content-Type' => 'application/json'], $response2->getHeaders());

        $result = $queryBuilder->find('post', ['id', 'title', 'text', 'status'], orderBy: ['id' => 'ASC']);
        $this->assert([
            ['id' => 1, 'title' => 'Title1', 'text' => 'New text1', 'status' => 'published'],
            ['id' => 2, 'title' => 'New title2', 'text' => 'Text2', 'status' => 'published'],
        ], $result);
    }
}
