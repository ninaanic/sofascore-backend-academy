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

    public function index(Request $request): Response
    {
        $slug = $request->attributes['_route_params']['slug'];
        $tournaments = $this->connection->query('SELECT tournament.name, tournament.slug
                                                FROM sport JOIN tournament 
                                                ON sport.Id = tournament.sport_id
                                                WHERE sport.slug LIKE :slug', ['slug' => $slug]);

        return new Response($this->templating->render('tournament/index.php', $tournaments));
    }

    /*
    public function json(Request $request): Response
    {
        $response = new Response(json_encode([
            'T1',
            'T2',
            'T3',
        ]));

        $response->addHeader('content-type', 'application/json');

        return $response;
    }
    */
}
