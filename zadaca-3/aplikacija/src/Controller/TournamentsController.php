<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
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

    private function getTournamentsFromDatabase(string $slug) {
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);
        $tournaments = $this->connection->find('tournament', ['name', 'slug'], ['sport_id' => $sport['id']]);

        return $tournaments;
    }

    public function index(string $slug): Response
    {
        $tournaments = $this->getTournamentsFromDatabase($slug);

        return new Response($this->templating->render('tournament/index.php', ['tournaments' => $tournaments]));
    }

    public function jsonTorunaments(string $slug): Response
    {
        $tournaments = $this->getTournamentsFromDatabase($slug);

        $response = new Response(json_encode($tournaments, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
