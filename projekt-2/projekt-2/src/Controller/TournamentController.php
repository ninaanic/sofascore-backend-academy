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
final class TournamentController
{
    public function __construct(
        //private readonly Templating $templating,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseListener $apiResponseListener,
    ) {
    }

    #[Route('/tournament/{id}/events', name: 'tournament_events', methods: 'GET')]
    public function events(int $id): Response
    {
        $tournament = $this->entityManager->getRepository(Tournament::class)->findOneBy(['id' => $id]);

        if (null === $tournament) {
            throw new HttpException(404, sprintf('A tournament with the id "%s" doesn\'t exist.', $id));
        }

        $events = $this->entityManager->getRepository(Event::class)->findBy(['tournament_id' => $tournament->getExternalId()]);

        return $this->apiResponseListener->onApiResponse($events);
    }


    #[Route('/tournament/{id}/details', name: 'tournament_details', methods: 'GET')]
    public function details(int $id): Response
    {
        $tournament = $this->entityManager->getRepository(Tournament::class)->findOneBy(['id' => $id]);

        if (null === $tournament) {
            throw new HttpException(404, sprintf('A tournament with the id "%s" doesn\'t exist.', $id));
        }

        return $this->apiResponseListener->onApiResponse($tournament);
    }
}