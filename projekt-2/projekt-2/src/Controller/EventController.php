<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Sport;
use App\Entity\Tournament;
use App\Entity\Event;
use App\Database\Connection;
use App\Tools\Templating\Templating;
use App\Attribute\ApiController;
use App\Listener\ApiResponseListener;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[ApiController]
#[AsController]
final class EventController
{
    public function __construct(
        //private readonly Templating $templating,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseListener $apiResponseListener,
    ) {
    }

    #[Route('/event/{id}/details', name: 'event_details', methods: 'GET')]
    public function details(int $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->findOneBy(['external_id' => $id]);

        if (null === $event) {
            throw new HttpException(404, sprintf('A event with the id "%s" doesn\'t exist.', $id));
        }

        return $this->apiResponseListener->onApiResponse($event);
    }
}