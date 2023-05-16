<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Templating\Templating;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class HomeController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Templating $templating,
    ) {
    }

    #[Route('/', name: 'home')]
    public function __invoke(): Response
    {
        $sports = $this->connection->find('sport', ['name', 'slug']);

        return new Response($this->templating->render('home/index.php', [
            'sports' => $sports,
        ]));
    }
}
