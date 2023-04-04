<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Response;
use SimpleFW\Templating\Templating;

final class PlayerController
{
    public function __construct(
        private readonly Templating $templating,
        private readonly Connection $connection,
    ) {
    }

    public function index(string $slug): Response
    {
        $team = $this->connection->findOne('team', ['id'], ['slug' => $slug]);
        if ($team !== null) {
            $players = $this->connection->find('player', ['name', 'slug'],  ['team_id' => $team['id']]);
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
        $players = $this->connection->find('player', ['name', 'slug'],  ['slug' => $slug]);

        if ($players === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($players, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }


}
