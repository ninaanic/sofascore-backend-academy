<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Entity\Team;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Response;
use SimpleFW\ORM\EntityManager;
use SimpleFW\Templating\Templating;

final class TeamController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function slug(string $slug): Response
    {
        $teams = $this->entityManager->findBy(Team::class, ['slug' => $slug]);

        if ($teams === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($teams, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
