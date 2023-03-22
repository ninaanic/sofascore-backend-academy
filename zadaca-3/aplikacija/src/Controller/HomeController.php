<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use SimpleFW\HTTP\Request;
use SimpleFW\HTTP\Response;
use SimpleFW\Templating\Templating;

final class HomeController
{
    public function __construct(
        private readonly Templating $templating,
        private readonly Connection $connection,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $sports = $this->connection->query('SELECT name, slug FROM Sport');

        return new Response($this->templating->render('home/index.php', $sports));
    }
}
