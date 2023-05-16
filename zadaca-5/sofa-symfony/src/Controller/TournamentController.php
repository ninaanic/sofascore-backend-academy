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
final class TournamentController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Templating $templating,
    ) {
    }

    #[Route('/sport/{slug}', name: 'sport')]
    public function index(string $slug): Response
    {
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);

        if (null === $sport) {
            throw new NotFoundHttpException(sprintf('A sport with the slug "%s" doesn\'t exist.', $slug));
        }

        $tournaments = $this->connection->find('tournament', ['name', 'slug'], ['sport_id' => $sport['id']]);

        return new Response($this->templating->render('tournament/index.php', [
            'tournaments' => $tournaments,
        ]));
    }
}
