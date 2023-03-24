<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use JsonException;
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

    private function getEventsFromDatabase(string $slug) {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);
        $events = $this->connection->find('event', ['id', 'start_date'], ['tournament_id' => $tournament['id']]);

        return $events;
    }

    private function getEventDetailsFromDatabase(int $id) {
        $event = $this->connection->findOne('event', [], ['id' => $id]);

        return $event;
    }


    public function index(string $slug): Response
    {
        $events = $this->getEventsFromDatabase($slug);

        return new Response($this->templating->render('event/index.php', ['events' => $events]));
    }

    public function details(int $id): Response
    {
        $event = $this->getEventDetailsFromDatabase($id);

        return new Response($this->templating->render('event/details.php', ['event' => $event]));
    }

    public function jsonEvents(string $slug): Response
    {
        $events = $this->getEventsFromDatabase($slug);

        $response = new Response(json_encode($events));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function jsonDetails(int $id): Response
    {
        $details = $this->getEventDetailsFromDatabase($id);

        $response = new Response(json_encode($details));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function patchEvent(int $id) : Response
    {
        $payload = file_get_contents('php://input');
            try {
                $payload = json_decode($payload, true, flags: \JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                http_response_code(400);
                $data = ['error' => 'Invalid json provided!'];
            }
            
        $this->connection->update('event', ['home_score' => $payload['home_score'], 'away_score' => $payload['away_score']], $id);
        $data = $this->connection->findOne('event', ['home_team_id', 'away_team_id', 'start_date', 'home_score', 'away_score'], ['id' => $id]);

        $response = new Response(json_encode($data));
        $response->addHeader('content-type', 'application/json');

        return $response;
        
    }
}