<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Attribute\ApiController;
use App\Database\Connection;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[ApiController]
#[AsController]
final class TournamentController
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    #[Route('/api/sport/{slug}', name: 'api-sport')]
    public function index(string $slug): array
    {
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);

        if (null === $sport) {
            throw new NotFoundHttpException(sprintf('A sport with the slug "%s" doesn\'t exist.', $slug));
        }

        return $this->connection->find('tournament', ['name', 'slug'], ['sport_id' => $sport['id']]);
    }
}
