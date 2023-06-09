<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Player;
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
final class PlayerController
{
    public function __construct(
        //private readonly Templating $templating,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseListener $apiResponseListener,
    ) {
    }

    // TODO nap kad se tablica popravi
    /*
    #[Route('/player/{id}/events', name: 'player', methods: 'GET')]
    public function events(int $id): Response
    {
        $player = $this->entityManager->getRepository(Player::class)->findOneBy(['external_id' => $id]);

        if (null === $player) {
            throw new HttpException(404, sprintf('A player with the id "%s" doesn\'t exist.', $id));
        }

        

        $events = [];
        $events = array_merge($events_for_home_team, $events_for_away_team);

        return $this->apiResponseListener->onApiResponse($events);
    }
    */

    #[Route('/player/{id}/details', name: 'player_details', methods: 'GET')]
    public function details(int $id): Response
    {
        $player = $this->entityManager->getRepository(Player::class)->findOneBy(['external_id' => $id]);

        if (null === $player) {
            throw new HttpException(404, sprintf('A player with the id "%s" doesn\'t exist.', $id));
        }

        return $this->apiResponseListener->onApiResponse($player);
    }
}