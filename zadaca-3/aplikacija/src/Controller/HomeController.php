<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use SimpleFW\HTTP\Response;
use SimpleFW\Templating\Templating;

final class HomeController
{
    public function __construct(
        private readonly Templating $templating,
        private readonly Connection $connection,
    ) {
    }

    private function getSportsFromDatabase() {
        $sports = $this->connection->find('sport', ['name', 'slug']);

        return $sports;
    }

    public function __invoke(): Response
    {
        $sports = $this->getSportsFromDatabase();

        return new Response($this->templating->render('home/index.php', ['sports' => $sports]));
    }

    public function jsonSports(): Response
    {
        $sports = $this->getSportsFromDatabase();

        $response = new Response(json_encode($sports, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
