<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Tools\Templating\Templating;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Templating $templating,
    ) {
    }

    #[Route('/', name: 'home', methods: 'GET')]
    public function __invoke(): Response
    {
        $sports = $this->connection->find('sport');

        $html = $this->templating->render('home/index.php', [
            'sports' => $sports,
        ]);

        return new Response($html);
    }
}
