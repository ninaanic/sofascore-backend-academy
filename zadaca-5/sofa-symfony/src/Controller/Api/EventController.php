<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Attribute\ApiController;
use App\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[ApiController]
#[AsController]
final class EventController
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    #[Route('/api/tournament/{slug}', name: 'api-tournament')]
    public function index(string $slug): array
    {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);

        if (null === $tournament) {
            throw new NotFoundHttpException(sprintf('A tournament with the slug "%s" doesn\'t exist.', $slug));
        }

        return $this->connection->find('event', ['id', 'start_date'], ['tournament_id' => $tournament['id']]);
    }

    #[Route('/api/event/{id}', name: 'api-event', requirements: ['id' => '\d+'], methods: 'GET')]
    public function detail(int $id): array
    {
        $event = $this->connection->findOne('event', [], ['id' => $id]);

        if (null === $event) {
            throw new NotFoundHttpException(sprintf('An event with the id "%d" doesn\'t exist.', $id));
        }

        return $event;
    }

    #[Route('/api/event/{id}', name: 'api-event-update', requirements: ['id' => '\d+'], methods: 'PATCH')]
    public function update(Request $request, int $id): array
    {
        $event = $this->connection->findOne('event', [], ['id' => $id]);

        if (null === $event) {
            throw new NotFoundHttpException(sprintf('An event with the id "%d" doesn\'t exist.', $id));
        }

        try {
            $payload = json_decode($request->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new NotFoundHttpException('Invalid json provided!');
        }

        $updateData = ['home_score' => $payload['home_score'], 'away_score' => $payload['away_score']];
        $this->connection->update('event', $updateData, $id);

        return array_merge($event, $updateData);
    }
}
