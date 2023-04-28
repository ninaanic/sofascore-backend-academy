<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Tools\Templating\Templating;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

final class TournamentController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Templating $templating,
    ) {
    }

    #[Route('/sport/{slug}', name: 'sport', methods: 'GET')]
    public function index(string $slug): Response
    {
        $sport = $this->connection->findOne('sport', ['id'], ['slug' => $slug]);

        if (null === $sport) {
            throw new HttpException(404, sprintf('A sport with the slug "%s" doesn\'t exist.', $slug));
        }

        $tournaments = $this->connection->find('tournament', ['name', 'slug'], ['sport_id' => $sport['id']]);

        return new Response($this->templating->render('tournament/index.php', [
            'tournaments' => $tournaments,
        ]));
    }
}