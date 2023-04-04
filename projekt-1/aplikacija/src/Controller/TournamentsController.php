<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Request;
use SimpleFW\HTTP\Response;
use SimpleFW\Templating\Templating;

final class TournamentsController
{
    public function __construct(
        private readonly Templating $templating,
        private readonly Connection $connection,
    ) {
    }

    private function getTournaments(string $slug) {
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);
        $tournaments = $this->connection->find('tournament', ['name', 'slug'], ['sport_id' => $sport['id']]);
        return $tournaments;
    }

    
    private function getTournamentsWithSlug(string $slug) {
        $tournaments = $this->connection->find('tournament', ['name', 'slug'],  ['slug' => $slug]);
        return $tournaments;
    }
    

    public function index(string $slug): Response
    {
        $tournaments = $this->getTournaments($slug);

        if ($tournaments === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($tournaments, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function slug(string $slug): Response
    {
        $tournament = $this->getTournamentsWithSlug($slug);

        if ($tournament === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($tournament, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
