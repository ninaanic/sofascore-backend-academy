<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Attribute\ApiController;
use App\Database\Connection;
use App\Listener\ApiResponseListener;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[ApiController]
#[AsController]
final class SportController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ApiResponseListener $apiResponseListener,
    ) {
    }

    #[Route('/api', name: 'api-home', methods: 'GET', requirements: ['_format' => 'json'], options: ['groups' => ['api', 'apiResponse']])]
    public function __invoke(): Response
    {
        $sports = $this->connection->find('sport', ['name', 'slug']);

        return $this->apiResponseListener->onApiResponse($sports);
    }
}
