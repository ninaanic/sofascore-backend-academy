<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Entity\Player;
use App\Entity\Team;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Response;
use SimpleFW\ORM\EntityManager;
use SimpleFW\Templating\Templating;

final class PlayerController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function index(string $slug): Response
    {
        $team = $this->entityManager->findOneBy(Team::class, ['slug' => $slug]);
        if ($team !== null) {
            $players = $this->entityManager->findBy(Player::class,  ['teamId' => $team->getId()]);
        } else {
            throw new HttpException(404, "404 not found");
        }

        if ($players === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($players, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function slug(string $slug): Response
    {
        $players = $this->entityManager->findBy(Player::class,  ['slug' => $slug]);

        if ($players === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($players, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }


}
