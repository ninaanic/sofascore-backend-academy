<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Sport;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Request;
use SimpleFW\HTTP\Response;
use SimpleFW\ORM\EntityManager;
use SimpleFW\Templating\Templating;

final class HomeController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function __invoke(): Response
    {
        $sports = $this->entityManager->findBy(Sport::class, [], ['id' => 'ASC']);

        if ($sports === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($sports));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function slug(string $slug): Response
    {
        $sport = $this->entityManager->findBy(Sport::class, ['slug' => $slug]);

        if ($sport === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($sport));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
