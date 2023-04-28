<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Database\Connection;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class EventController
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    #[Route('/api/tournament/{slug}', name: 'api-tournament', methods: 'GET')]
    public function index(string $slug): Response
    {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);

        if (null === $tournament) {
            throw new HttpException(404, json_encode([
                'error' => sprintf('A tournament with the slug "%s" doesn\'t exist.', $slug),
            ]), headers: ['Content-Type' => 'application/json']);
        }

        $events = $this->connection->find('event', ['id', 'start_date'], ['tournament_id' => $tournament['id']]);

        return new Response(json_encode($events), headers: ['Content-Type' => 'application/json']);
    }

    #[Route('/api/event/{id}', name: 'api-event', methods: 'GET')]
    public function detail(int $id): Response
    {
        $event = $this->connection->findOne('event', [], ['id' => $id]);

        if (null === $event) {
            throw new HttpException(404, json_encode([
                'error' => sprintf('An event with the id "%d" doesn\'t exist.', $id),
            ]), headers: ['Content-Type' => 'application/json']);
        }

        return new Response(json_encode($event), headers: ['Content-Type' => 'application/json']);
    }

    #[Route('/api/event/{id}', name: 'api-event-update', methods: 'PATCH')]
    public function update(Request $request, int $id): Response
    {
        $event = $this->connection->findOne('event', [], ['id' => $id]);

        if (null === $event) {
            throw new HttpException(404, json_encode([
                'error' => sprintf('An event with the id "%d" doesn\'t exist.', $id),
            ]), headers: ['Content-Type' => 'application/json']);
        }

        try {
            $payload = json_decode($request->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new HttpException(400, json_encode([
                'error' => 'Invalid json provided!',
            ]), headers: ['Content-Type' => 'application/json']);
        }

        $updateData = ['home_score' => $payload['home_score'], 'away_score' => $payload['away_score']];
        $this->connection->update('event', $updateData, $id);

        return new Response(json_encode(array_merge($event, $updateData)), headers: ['Content-Type' => 'application/json']);
    }
}
