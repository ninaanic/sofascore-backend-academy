<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Request;
use SimpleFW\HTTP\Response;
use SimpleFW\Templating\Templating;

final class HomeController
{
    public function __construct(
        private readonly Templating $templating,
        private readonly Connection $connection,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $sports = $this->connection->find('sport', ['name']);

        if ($sports === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($sports, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function slug(string $slug): Response
    {
        $sport = $this->connection->find('sport', ['name', 'slug'],  ['slug' => $slug]);

        if ($sport === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($sport, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
