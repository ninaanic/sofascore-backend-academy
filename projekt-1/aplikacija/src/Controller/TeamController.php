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

    /*

    private function getEvents(string $slug) {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);
        $events = $this->connection->find('event', ['id', 'start_date'], ['tournament_id' => $tournament['id']]);
        return $events;
    }

    */

    private function getTeamsWithSlug(string $slug) {
        $teams = $this->connection->find('team', ['name', 'slug'],  ['slug' => $slug]);
        return $teams;
    }

    /*
    public function index(string $slug): Response
    {
        $events = $this->getEvents($slug);

        if ($events === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($events, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
    */


    public function slug(string $slug): Response
    {
        $team = $this->getTeamsWithSlug($slug);

        if ($team === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($team, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
