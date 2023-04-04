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

    /*

    private function getEvents(string $slug) {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);
        $events = $this->connection->find('event', ['id', 'start_date'], ['tournament_id' => $tournament['id']]);
        return $events;
    }

    */

    private function getPlayers(string $slug) {
        $team = $this->connection->findOne('team', ['id'], ['slug' => $slug]);
        $players = $this->connection->find('player', ['name', 'slug'],  ['team_id' => $team['id']]);
        return $players;
    }

    private function getPlayersWithSlug(string $slug) {
        $teams = $this->connection->find('player', ['name', 'slug'],  ['slug' => $slug]);
        return $teams;
    }

    public function index(string $slug): Response
    {
        $player = $this->getPlayers($slug);

        if ($player === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($player, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function slug(string $slug): Response
    {
        $player = $this->getPlayersWithSlug($slug);

        if ($player === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($player, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }


}
