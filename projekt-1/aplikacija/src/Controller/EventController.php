<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use DateTimeImmutable;
use JsonException;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Request;
use SimpleFW\HTTP\Response;
use SimpleFW\ORM\EntityManager;
use SimpleFW\Templating\Templating;

final class EventController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function index(string $slug): Response
    {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);
        if ($tournament !== null) {
            $events = $this->connection->find('event', ['id', 'start_date'], ['tournament_id' => $tournament['id']]);
        } else {
            throw new HttpException(404, "404 not found");
        }
    
        if ($events === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($events, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function slug(string $slug): Response
    {
        $event = $this->connection->find('event', ['id', 'slug', 'start_date', 'home_score', 'away_score'],  ['slug' => $slug]);

        if ($event === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function update(string $slug): Response
    {
        $event = $this->connection->findOne('event', ['id'],  ['slug' => $slug]);
        $eventId = $event['id'];

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

        $event = $this->connection->find('event', ['id', 'slug', 'start_date', 'home_score', 'away_score'],  ['slug' => $slug]);
        
        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
    public function delete(string $slug): Response
    {
        $event = $this->connection->findOne('event', ['id'],  ['slug' => $slug]);

        if ($event === null) {
            throw new HttpException(404, "404 not found");
        }

        $rowsAffected = $this->connection->delete('event', $event['id']);

        if ($rowsAffected > 0) {
            $response = new Response("200 OK", 200);
        } else {
            $response = new Response("500 Internal Server Error", 500);
        }

        $response->addHeader('content-type', 'application/json');
        return $response;
    }
    
    public function team_tournament_slug(string $slug, string $tournamentSlug): Response
    {
        $team = $this->connection->findOne('team', ['id'], ['slug' => $slug]);
        if ($team !== null) {
            $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $tournamentSlug]);
            if ($tournament !== null) {
                $events_home = $this->connection->find('event', ['id', 'start_date'],  ['home_team_id' => $team['id'], 'tournament_id' => $tournament['id']]); 
                $events_away = $this->connection->find('event', ['id', 'start_date'],  ['away_team_id' => $team['id'], 'tournament_id' => $tournament['id']]);
            } else {
                throw new HttpException(404, "404 not found");
            }
        } else {
            throw new HttpException(404, "404 not found");
        }

        if ($events_home === [] && $events_away === []) {
            throw new HttpException(404, "404 not found");
        }

        $all_events = array_merge($events_home, $events_away);

        $response = new Response(json_encode($all_events, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function event_team(string $slug): Response
    {
        $team = $this->connection->findOne('team', ['id'], ['slug' => $slug]);
        if ($team !== null) {
            $events_home = $this->connection->find('event', ['id', 'start_date'], ['home_team_id' => $team['id']]);
            $events_away = $this->connection->find('event', ['id', 'start_date'], ['away_team_id' => $team['id']]);

        } else {
            throw new HttpException(404, "404 not found");
        }

        if ($events_home === [] && $events_away === []) {
            throw new HttpException(404, "404 not found");
        }

        $all_events = array_merge($events_home, $events_away);

        $response = new Response(json_encode($all_events, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function date_sport(string $slug, string $date): Response
    {
        $events = [];
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);
        if ($sport !== null) {
            $tournament = $this->connection->find('tournament', ['id'], ['sport_id' => $sport['id']]);
            if ($tournament !== []) {
                foreach ($tournament as $t) {
                    $result = $this->connection->find('event', ['id', 'start_date'], ['start_date' => $date, 'tournament_id' => $t['id']]);
                    if ($result !== []) {
                        array_push($events, $result);
                    }
                }
            } else {
                throw new HttpException(404, "404 not found");
            }
        } else {
            throw new HttpException(404, "404 not found");
        }

        if ($events === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($events, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function date_tournament(string $slug, string $date): Response
    {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);
        if ($tournament !== null) {
            $events = $this->connection->find('event', ['id', 'start_date'], ['start_date' => $date, 'tournament_id' => $tournament['id']]);
        } else {
            throw new HttpException(404, "404 not found");
        }

        if ($events === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($events, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

}
