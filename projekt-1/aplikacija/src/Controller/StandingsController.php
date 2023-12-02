<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Entity\Tournament;
use App\Entity\Standings;
use App\Entity\Team;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Response;
use SimpleFW\ORM\EntityManager;
use SimpleFW\Templating\Templating;

final class StandingsController
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function index(string $slug): Response
    {
        $tournament = $this->entityManager->findOneBy(Tournament::class, ['slug' => $slug]);
        if ($tournament !== null) {
            $standings = $this->entityManager->findBy(Standings::class, ['tournamentId' => $tournament->getId()], ['position' => 'ASC']);
        } else {
            throw new HttpException(404, "404 not found");
        }

        if ($standings === []) {
            throw new HttpException(404, "404 not found");
        } else {
            $result = array();
            $result['tournament'] = $tournament->jsonSerialize();
            $team_standings = array();
            foreach($standings as $standing) {
                $tmp = array();
                $team = $this->entityManager->findBy(Team::class, ['id' => $standing->getTeamId()]);
                $tmp['team'] = $team;
                $arr_merge = array_merge($tmp, $standing->jsonSerialize());
                array_push($team_standings, $arr_merge);
            } 
            $result['rows'] = $team_standings;
        }

        $response = new Response(json_encode($result, JSON_PRETTY_PRINT));
        $response->addHeader('content-type', 'application/json');

        return $response;
    }
}
