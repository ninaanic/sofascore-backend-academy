<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Database\Connection;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TournamentController
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    #[Route('/api/sport/{slug}', name: 'api-sport', methods: 'GET')]
    public function index(string $slug): Response
    {
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);

        if (null === $sport) {
            throw new HttpException(404, json_encode([
                'error' => sprintf('A sport with the slug "%s" doesn\'t exist.', $slug),
            ]), headers: ['Content-Type' => 'application/json']);
        }

        $tournaments = $this->connection->find('tournament', ['name', 'slug'], ['sport_id' => $sport['id']]);

        return new Response(json_encode($tournaments), headers: ['Content-Type' => 'application/json']);
    }
}
