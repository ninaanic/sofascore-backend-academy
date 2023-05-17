<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Attribute\ApiController;
use App\Database\Connection;
use App\Listener\ApiResponseListener;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[ApiController]
#[AsController]
final class EventController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ApiResponseListener $apiResponseListener,
    ) {
    }

    #[Route('/api/tournament/{slug}', name: 'api-tournament', methods: 'GET', requirements: ['_format' => 'json'], options: ['groups' => ['api', 'apiResponse']])]
    public function index(string $slug): Response
    {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);

        if (null === $tournament) {
            throw new NotFoundHttpException("A tournament with the slug $slug does not exist.");
        }

        $events = $this->connection->find('event', ['id', 'start_date'], ['tournament_id' => $tournament['id']]);

        return $this->apiResponseListener->onApiResponse($events);
    }

    #[Route('/api/event/{id}', name: 'api-event', methods: 'GET', requirements: ['_format' => 'json'], options: ['groups' => ['api', 'apiResponse']])]
    public function detail(int $id): Response
    {
        $event = $this->connection->findOne('event', [], ['id' => $id]);

        if (null === $event) {
            throw new NotFoundHttpException("An event with the id $id does not exist.");
        }

        return $this->apiResponseListener->onApiResponse($event);
    }

    #[Route('/api/event/{id}', name: 'api-event-update', methods: 'PATCH', requirements: ['_format' => 'json'], options: ['groups' => ['api', 'apiResponse']])]
    public function update(Request $request, int $id): Response
    {
        $event = $this->connection->findOne('event', [], ['id' => $id]);

        if (null === $event) {
            throw new NotFoundHttpException("An event with the id $id does not exist.");
        }

        try {
            $payload = json_decode($request->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new NotFoundHttpException("Invalid json provided!");
        }

        $updateData = ['home_score' => $payload['home_score'], 'away_score' => $payload['away_score']];
        $this->connection->update('event', $updateData, $id);

        return $this->apiResponseListener->onApiResponse(array_merge($event, $updateData));
    }
}
