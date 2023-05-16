<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Attribute\ApiController;
use App\Database\Connection;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[ApiController]
#[AsController]
final class SportController
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    #[Route('/api', name: 'api-home')]
    public function __invoke(): array
    {
        return $this->connection->find('sport', ['name', 'slug']);
    }
}
