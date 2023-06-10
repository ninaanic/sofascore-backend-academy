<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Entity\Event;
use App\Database\Connection;
use App\Tools\Templating\Templating;
use App\Attribute\ApiController;
use App\Entity\Standings;
use App\Entity\Tournament;
use App\Listener\ApiResponseListener;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[ApiController]
#[AsController]
final class StandingsController
{
    public function __construct(
        //private readonly Templating $templating,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiResponseListener $apiResponseListener,
    ) {
    }

    #[Route('/tournament/{id}/standings', name: 'standings', methods: 'GET')]
    public function standings(int $id): Response
    {
        $tournament = $this->entityManager->getRepository(Tournament::class)->findOneBy(['external_id' => $id]);

        if (null === $tournament) {
            throw new HttpException(404, sprintf('A team with the id "%s" doesn\'t exist.', $id));
        }

        $sport_id = $tournament->getSportId();

        if ($sport_id != 1) {
            $standings = $this->entityManager->getRepository(Standings::class)->findBy(['tournament_id' => $tournament->getExternalId()]);
        } else {
            // sort za nogomet
            $standings = $this->entityManager->getRepository(Standings::class)->findBy(['tournament_id' => $tournament->getExternalId()], ['points' => 'DESC']);
        }

        return $this->apiResponseListener->onApiResponse($standings);
    }
}