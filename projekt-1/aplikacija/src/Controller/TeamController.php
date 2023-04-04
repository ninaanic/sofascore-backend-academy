<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Response;
use SimpleFW\Templating\Templating;

final class TeamController
{
    public function __construct(
        private readonly Templating $templating,
        private readonly Connection $connection,
    ) {
    }

    public function slug(string $slug): Response
    {
        $teams = $this->connection->find('team', ['name', 'slug'],  ['slug' => $slug]);

        if ($teams === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($teams, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
