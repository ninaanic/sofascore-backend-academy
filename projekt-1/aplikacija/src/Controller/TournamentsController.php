<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Entity\Sport;
use App\Entity\Tournament;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Request;
use SimpleFW\HTTP\Response;
use SimpleFW\ORM\EntityManager;
use SimpleFW\Templating\Templating;

final class TournamentsController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }
    
    public function index(string $slug): Response
    {
        $sport = $this->entityManager->findOneBy(Sport::class, ['slug' => $slug]);
        if ($sport !== null) {
            $tournaments = $this->entityManager->findBy(Tournament::class, ['sportId' => $sport->getId()]);
        } else {
            throw new HttpException(404, "404 not found");
        }
        
        if ($tournaments === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($tournaments, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function slug(string $slug): Response
    {
        $tournament = $this->entityManager->findBy(Tournament::class, ['slug' => $slug]);

        if ($tournament === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($tournament, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
