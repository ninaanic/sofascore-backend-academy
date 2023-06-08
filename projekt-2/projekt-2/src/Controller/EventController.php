<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Tools\Templating\Templating;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class EventController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Templating $templating,
    ) {
    }

    #[Route('/sport/{slug}/events/{date}', name: 'sport', methods: 'GET')]
    public function sportDate(string $slug): Response
    {
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);

        if (null === $sport) {
            throw new HttpException(404, sprintf('A sport with the slug "%s" doesn\'t exist.', $slug));
        }

        $events = $this->connection->find('event', ['id', 'start_date'], ['sport_id' => $sport['id']]);

        return new Response($this->templating->render('event/sportDate.php', [
            'events' => $events,
        ]));
    }

    #[Route('/event/{id}', name: 'event', methods: 'GET')]
    public function detail(int $id): Response
    {
        $event = $this->connection->findOne('event', [], ['id' => $id]);

        if (null === $event) {
            throw new HttpException(404, sprintf('An event with the id "%d" doesn\'t exist.', $id));
        }

        return new Response($this->templating->render('event/detail.php', [
            'event' => $event,
        ]));
    }
}