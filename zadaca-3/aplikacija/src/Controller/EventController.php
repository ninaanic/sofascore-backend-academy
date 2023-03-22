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
        private readonly Connection $connection,
    ) {
    }

    // TODO popravit 
    public function details(Request $request): Response
    {
        $eventId = $request->attributes['_route_params']['id'];

        $event = $this->connection->query('SELECT * FROM event WHERE id = :id', ['id' => $eventId]);
    }
}