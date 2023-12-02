<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Entity\Event;
use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Tournament;
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
        $tournament = $this->entityManager->findOneBy(Tournament::class, ['slug' => $slug]);
        if ($tournament !== null) {
            $events = $this->entityManager->findBy(Event::class, ['tournamentId' => $tournament->getId()]);
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
        $event = $this->entityManager->findBy(Event::class, ['slug' => $slug]);

        if ($event === []) {
            throw new HttpException(404, "404 not found");
        }

        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }

    public function update(string $slug): Response
    {
        $event = $this->entityManager->findOneBy(Event::class, ['slug' => $slug]);

        if ($event->getId() === null) {
            throw new HttpException(404, "404 not found");
        }

        $payload = file_get_contents('php://input');
        try {
            $payload = json_decode($payload, true, flags: \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new HttpException(404, "404 not found");
        }

        if (isset($payload['home_score'])) {
            $event->setHomeScore($payload['home_score']);
        }
        if (isset($payload['away_score'])) {
            $event->setAwayScore($payload['away_score']);
        }

        $this->entityManager->flush();
        
        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
    public function delete(string $slug): Response
    {
        $event = $this->entityManager->findOneBy(Event::class, ['slug' => $slug]);

        if ($event === null) {
            throw new HttpException(404, "404 not found");
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();

        $response = new Response(json_encode($event, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
    
    public function team_tournament_slug(string $slug, string $tournamentSlug): Response
    {
        $team = $this->entityManager->findOneBy(Team::class, ['slug' => $slug]);
        if ($team !== null) {
            $tournament = $this->entityManager->findOneBy(Tournament::class, ['slug' => $tournamentSlug]);
            if ($tournament !== null) {
                $events_home = $this->entityManager->findBy(Event::class, ['homeTeamId' => $team->getId(), 'tournamentId' => $tournament->getId()]); 
                $events_away = $this->entityManager->findBy(Event::class, ['awayTeamId' => $team->getId(), 'tournamentId' => $tournament->getId()]);
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
        $team = $this->entityManager->findOneBy(Team::class, ['slug' => $slug]);
        if ($team !== null) {
            $events_home = $this->entityManager->findBy(Event::class, ['homeTeamId' => $team->getId()]);
            $events_away = $this->entityManager->findBy(Event::class, ['awayTeamId' => $team->getId()]);

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
        $sport = $this->entityManager->findOneBy(Sport::class, ['slug' => $slug]);
        if ($sport !== null) {
            $tournament = $this->entityManager->findBy(Tournament::class, ['sportId' => $sport->getId()]);
            if ($tournament !== []) {
                foreach ($tournament as $t) {
                    $result = $this->entityManager->findBy(Event::class, ['startDate' => $date, 'tournamentId' => $t->getId()]);
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
        $tournament = $this->entityManager->findOneBy(Tournament::class, ['slug' => $slug]);
        if ($tournament !== null) {
            $events = $this->entityManager->findBy(Event::class, ['startDate' => $date, 'tournamentId' => $tournament->getId()]);
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
