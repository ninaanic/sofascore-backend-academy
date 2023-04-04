<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use DateTimeImmutable;
use JsonException;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Request;
use SimpleFW\HTTP\Response;
use SimpleFW\Templating\Templating;

final class EventController
{
    public function __construct(
        private readonly Templating $templating,
        private readonly Connection $connection,
    ) {
    }

    private function getEvents(string $slug) {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);
        $events = $this->connection->find('event', ['id', 'start_date'], ['tournament_id' => $tournament['id']]);
        return $events;
    }

    // review - ne samo home_team_id nego i away
    private function getEventsTeam(string $slug) {
        $team = $this->connection->findOne('team', ['id'], ['slug' => $slug]);
        $events = $this->connection->find('event', ['id', 'start_date'], ['home_team_id' => $team['id']]);
        return $events;
    }

    /*
    private function getEventsOnDate(string $slug, DateTimeImmutable $date) {
        // todo dovršit 
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);
        $tournaments = $this->connection->find('event', ['id', 'slug'], ['sport_id' => $sport['id']]);
        return $tournaments;
    }
    */

    private function getEventsWithSlug(string $slug) {
        $events = $this->connection->find('event', ['id', 'slug', 'start_date', 'home_score', 'away_score'],  ['slug' => $slug]);
        return $events;
    }
    
    private function getEventId(string $slug) {
        $eventId = $this->connection->findOne('event', ['id'],  ['slug' => $slug]);
        return $eventId['id'];
    }

    // review - ne samo home_team_id nego i away
    private function getEventsWithTeamAndTournamentSlug(string $slug, string $tournamentSlug) {
        $team = $this->connection->findOne('team', ['id'], ['slug' => $slug]);
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $tournamentSlug]);
        $events = $this->connection->find('event', ['id', 'slug', 'start_date', 'home_score', 'away_score'],  ['home_team_id' => $team['id'], 'tournament_id' => $tournament['id']]);
        return $events;
    }




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

    /*
    public function eventOnDateS(string $slug, DateTimeImmutable $date): Response
    {
        $events = $this->getEventsOnDate($slug, $date);

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
        $event = $this->getEventsWithSlug($slug);

        if ($event === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function update(string $slug): Response
    {
        $eventId = $this->getEventId($slug);

        if ($eventId === null) {
            throw new HttpException(404, "404 not found");
        }

        $payload = file_get_contents('php://input');
        try {
            $payload = json_decode($payload, true, flags: \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new HttpException(404, "404 not found");
        }
        $this->connection->update('event', ['home_score' => $payload['home_score'], 'away_score' => $payload['away_score']], $eventId);

        $event = $this->getEventsWithSlug($slug);
        
        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }


    /*
    public function delete(string $slug): Response
    {
        $event = $this->getEventsWithSlug($slug);

        if ($event === []) {
            throw new HttpException(404, "404 not found");
        }

        // todo delete

        $payload = file_get_contents('php://input');
        try {
            $payload = json_decode($payload, true, flags: \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new HttpException(404, "404 not found");
        }
        $this->connection->update('event', ['home_score' => $payload['home_score'], 'away_score' => $payload['away_score']], $eventId);

        $event = $this->getEventsWithSlug($slug);
        
        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
    */

    public function teamSlug_tournamentSlug(string $slug, string $tournamentSlug): Response
    {
        $event = $this->getEventsWithTeamAndTournamentSlug($slug, $tournamentSlug);

        if ($event === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }


    public function event_team(string $slug): Response
    {
        $event = $this->getEventsTeam($slug);

        if ($event === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
