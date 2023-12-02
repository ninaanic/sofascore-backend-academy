<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostStatusEnum;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Request;
use SimpleFW\HTTP\Response;
use SimpleFW\ORM\EntityManager;

final class PostController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function index(): Response
    {
        $post = $this->entityManager->findBy(Post::class, [], ['id' => 'ASC']);

        return new Response(json_encode($post), headers: ['Content-Type' => 'application/json']);
    }

    public function show(int $id): Response
    {
        $post = $this->entityManager->find(Post::class, $id);

        if (null === $post) {
            throw new HttpException(404, sprintf('Unknown post with id %d.', $id));
        }

        return new Response(json_encode($post), headers: ['Content-Type' => 'application/json']);
    }

    public function create(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        $post = new Post($payload['title'], $payload['text'], isset($payload['status']) ? PostStatusEnum::from($payload['status']) : null);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return new Response(json_encode($post), headers: ['Content-Type' => 'application/json']);
    }

    public function update(Request $request, int $id): Response
    {
        $post = $this->entityManager->find(Post::class, $id);

        if (null === $post) {
            throw new HttpException(404, sprintf('Unknown post with id %d.', $id));
        }

        $payload = json_decode($request->getContent(), true);

        if (isset($payload['title'])) {
            $post->setTitle($payload['title']);
        }
        if (isset($payload['text'])) {
            $post->setText($payload['text']);
        }
        if (isset($payload['status'])) {
            $post->setStatus(PostStatusEnum::from($payload['status']));
        }

        $this->entityManager->flush();

        return new Response(json_encode($post), headers: ['Content-Type' => 'application/json']);
    }

    public function delete(int $id): Response
    {
        $post = $this->entityManager->find(Post::class, $id);

        if (null === $post) {
            throw new HttpException(404, sprintf('Unknown post with id %d.', $id));
        }

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return new Response(json_encode($post), headers: ['Content-Type' => 'application/json']);
    }
}
