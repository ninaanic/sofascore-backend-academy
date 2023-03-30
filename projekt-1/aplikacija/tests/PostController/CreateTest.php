<?php

declare(strict_types=1);

namespace App\Tests\PostController;

use App\Controller\PostController;
use App\Tests\AssertTrait;
use App\Tests\KernelTestCase;
use SimpleFW\HTTP\Request;
use SimpleFW\ORM\Connection;
use SimpleFW\ORM\QueryBuilder;

final class CreateTest extends KernelTestCase
{
    use AssertTrait;

    public function run(): void
    {
        $kernel = self::createKernel();

        $kernel->getContainer()->get(Connection::class)->execute('TRUNCATE TABLE post RESTART IDENTITY CASCADE');

        $postController = $kernel->getContainer()->get(PostController::class);

        $response1 = $postController->create(new Request(content: json_encode(['title' => 'Title1', 'text' => 'Text1', 'status' => 'published'])));
        $content1 = json_decode($response1->getContent(), true);
        $this->assert(['id', 'title', 'text', 'status', 'dateCreated'], array_keys($content1));
        $this->assert($content1['title'], 'Title1');
        $this->assert($content1['text'], 'Text1');
        $this->assert($content1['status'], 'published');
        $this->assert(['Content-Type' => 'application/json'], $response1->getHeaders());

        $response2 = $postController->create(new Request(content: json_encode(['title' => 'Title2', 'text' => 'Text2'])));
        $content2 = json_decode($response2->getContent(), true);
        $this->assert(['id', 'title', 'text', 'status', 'dateCreated'], array_keys($content2));
        $this->assert($content2['title'], 'Title2');
        $this->assert($content2['text'], 'Text2');
        $this->assert($content2['status'], 'draft');
        $this->assert(['Content-Type' => 'application/json'], $response2->getHeaders());

        $queryBuilder = $kernel->getContainer()->get(QueryBuilder::class);

        $result = $queryBuilder->find('post', ['id', 'title', 'text', 'status'], orderBy: ['id' => 'ASC']);
        $this->assert([
            ['id' => 1, 'title' => 'Title1', 'text' => 'Text1', 'status' => 'published'],
            ['id' => 2, 'title' => 'Title2', 'text' => 'Text2', 'status' => 'draft'],
        ], $result);
    }
}
