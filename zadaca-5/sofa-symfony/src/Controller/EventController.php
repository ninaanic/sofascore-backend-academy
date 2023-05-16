<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Templating\Templating;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class EventController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Templating $templating,
    ) {
    }

    #[Route('/tournament/{slug}', name: 'tournament')]
    public function index(string $slug): Response
    {
        $tournament = $this->connection->findOne('tournament', ['id'], ['slug' => $slug]);

        if (null === $tournament) {
            throw new NotFoundHttpException(sprintf('A tournament with the slug "%s" doesn\'t exist.', $slug));
        }

        $events = $this->connection->find('event', ['id', 'start_date'], ['tournament_id' => $tournament['id']]);

        return new Response($this->templating->render('event/index.php', [
            'events' => $events,
        ]));
    }

    #[Route('/event/{id}', name: 'event', requirements: ['id' => '\d+'])]
    public function detail(int $id): Response
    {
        $event = $this->connection->findOne('event', [], ['id' => $id]);

        if (null === $event) {
            throw new NotFoundHttpException(sprintf('An event with the id "%d" doesn\'t exist.', $id));
        }

        return new Response($this->templating->render('event/detail.php', [
            'event' => $event,
        ]));
    }
}
