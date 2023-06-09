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
final class SportController
{
    public function __construct(
        //private readonly Templating $templating,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseListener $apiResponseListener,
    ) {
    }

    #[Route('/sport/{slug}/events/{date}', name: 'sport', methods: 'GET')]
    public function events(string $slug, string $date): Response
    {
        $sport = $this->entityManager->getRepository(Sport::class)->findOneBy(['slug' => $slug]);

        if (null === $sport) {
            throw new HttpException(404, sprintf('A sport with the slug "%s" doesn\'t exist.', $slug));
        }

        $tournaments = $this->entityManager->getRepository(Tournament::class)->findBy(['sport_id' => $sport->getExternalId()]);
        
        $events = [];
        foreach ($tournaments as $tournament) {
            $events = array_merge($events, $this->entityManager->getRepository(Event::class)->findBy(['start_date' =>  $date, 'tournament_id' => $tournament->getExternalId()]));
        }

        return $this->apiResponseListener->onApiResponse($events);
    }
}