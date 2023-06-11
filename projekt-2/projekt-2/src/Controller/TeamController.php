<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
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
final class TeamController
{
    public function __construct(
        //private readonly Templating $templating,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseListener $apiResponseListener,
    ) {
    }

    #[Route('/team/{id}/events', name: 'team', methods: 'GET')]
    public function events(int $id): Response
    {
        $team = $this->entityManager->getRepository(Team::class)->findOneBy(['id' => $id]);

        if (null === $team) {
            throw new HttpException(404, sprintf('A team with the id "%s" doesn\'t exist.', $id));
        }

        $events_for_home_team = $this->entityManager->getRepository(Event::class)->findBy(['home_team_id' => $team->getExternalId()]);
        $events_for_away_team = $this->entityManager->getRepository(Event::class)->findBy(['away_team_id' => $team->getExternalId()]);

        $events = [];
        $events = array_merge($events_for_home_team, $events_for_away_team);

        return $this->apiResponseListener->onApiResponse($events);
    }

    #[Route('/team/{id}/details', name: 'team_details', methods: 'GET')]
    public function details(int $id): Response
    {
        $team = $this->entityManager->getRepository(Team::class)->findOneBy(['id' => $id]);

        if (null === $team) {
            throw new HttpException(404, sprintf('A team with the id "%s" doesn\'t exist.', $id));
        }

        return $this->apiResponseListener->onApiResponse($team);
    }

    #[Route('/team/{id}/players', name: 'team_players', methods: 'GET')]
    public function players(int $id): Response
    {
        $team = $this->entityManager->getRepository(Team::class)->findOneBy(['id' => $id]);

        if (null === $team) {
            throw new HttpException(404, sprintf('A team with the id "%s" doesn\'t exist.', $id));
        }

        $players = $this->entityManager->getRepository(Player::class)->findBy(['team_id' => $team->getExternalId()]);

        return $this->apiResponseListener->onApiResponse($players);
    }
}