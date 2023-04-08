<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Response;
use SimpleFW\Templating\Templating;

final class StandingsController
{
    public function __construct(
        private readonly Templating $templating,
        private readonly Connection $connection,
    ) {
    }

    public function index(string $slug): Response
    {
        $tournament = $this->connection->findOne('tournament', ['id', 'name', 'slug'], ['slug' => $slug]);
        if ($tournament !== null) {
            $standings = $this->connection->find('standings', [], ['tournament_id' => $tournament['id']]);
        } else {
            throw new HttpException(404, "404 not found");
        }

        if ($standings === []) {
            throw new HttpException(404, "404 not found");
        } else {
            $result = array();
            $result['tournament'] = $tournament;
            $team_standings = array();
            foreach($standings as $standing) {
                $tmp = array();
                $team = $this->connection->find('team', ['id', 'name', 'slug'], ['id' => $standing['team_id']]);
                $tmp['team'] = $team;
                $arr_merge = array_merge($tmp, $standing);
                array_push($team_standings, $arr_merge);
            } 
            $result['rows'] = $team_standings;
        }

        $response = new Response(json_encode($result, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
