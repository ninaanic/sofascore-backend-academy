<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use SimpleFW\HTTP\Request;
use SimpleFW\HTTP\Response;
use SimpleFW\Templating\Templating;

class EventController
{
    public function __construct(
        private readonly Templating $templating,
        private readonly Connection $connection,
    ) {
    }

    public function details(Request $request): Response
    {
        $eventId = $request->attributes['_route_params']['id'];
        $events = $this->connection->query('SELECT * FROM event WHERE id = :id', ['id' => $eventId]);

        return new Response($this->templating->render('event/index.php', $events));
    }
}