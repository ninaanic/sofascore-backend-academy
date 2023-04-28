<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Database\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SportController
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    #[Route('/api', name: 'api-home', methods: 'GET')]
    public function __invoke(): Response
    {
        $sports = $this->connection->find('sport', ['name', 'slug']);

        return new Response(json_encode($sports), headers: ['Content-Type' => 'application/json']);
    }
}
