<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Attribute\ApiController;
use App\Database\Connection;
use App\Listener\ApiResponseListener;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[ApiController]
#[AsController]
final class TournamentController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ApiResponseListener $apiResponseListener,
    ) {
    }

    #[Route('/api/sport/{slug}', name: 'api-sport', methods: 'GET', requirements: ['_format' => 'json'], options: ['groups' => ['api', 'apiResponse']])]
    public function index(string $slug): Response
    {
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);

        if (null === $sport) {
            throw new NotFoundHttpException("A sport with the slug $slug does not exist.");
        }

        $tournaments = $this->connection->find('tournament', ['name', 'slug'], ['sport_id' => $sport['id']]);

        return $this->apiResponseListener->onApiResponse($tournaments);
    }
}
