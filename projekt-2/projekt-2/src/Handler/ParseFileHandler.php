<?php

namespace App\Handler;

use App\Entity\EventStatusEnum;
use App\Entity\Sport;
use App\Message\ParseFile;
use App\Database\Connection;
use App\Entity\Event;
use App\Entity\Event as EntityEvent;
use App\Entity\Player;
use App\Entity\Standings;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Parser\JsonParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;


class ParseFileHandler
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }
    public function __invoke(ParseFile $parseFile)
    {
        echo 'Processing '.$parseFile->filename, \PHP_EOL;

        $data = file_get_contents($parseFile->filename);

        switch ($parseFile->filename) {
            case "data/sports.json":
                $this->parseSports($data);
                break;
            case "data/tournaments.json":
                $this->parseTournaments($data);
                break;
            case "data/events.json":
                $this->parseEvents($data);
                break;
            case "data/teams.json":
                $this->parseTeams($data);
                break;
            case "data/players.json":
                $this->parsePlayers($data);
                break;
            case "data/standings.json":
                $this->parseStandings($data);
                break;
        }
    }

    public function parseSports(string $data) {

        $sports = $this->serializer->deserialize($data, \App\DTO\Sport::class.'[]', 'json');
    
        foreach ($sports as $sport) {
            // get Entity
            $sportEntity = $this->entityManager->getRepository(Sport::class)->findOneBy(['external_id' => $sport->id]);

            if (null === $sportEntity) {
                // create Entity
                $sportEntity = new Sport($sport->name, $sport->slug, $sport->id);
                $this->entityManager->persist($sportEntity);
            } else {
                // update Entity
                $sportEntity->setName($sport->name);
                $sportEntity->setSlug($sport->slug);
            }
        }

        $this->entityManager->flush();

        echo 'Sports persisted successfully.', \PHP_EOL;
    }

    public function parseTournaments(string $data) {

        $tournamnets = $this->serializer->deserialize($data, \App\DTO\Tournament::class.'[]', 'json');

        //var_dump($tournamnets);
        
        foreach ($tournamnets as $tournament) {
            // get Entity
            $tournamentEntity = $this->entityManager->getRepository(Tournament::class)->findOneBy(['external_id' => $tournament->id]);
            //var_dump($tournamentEntity);

            if (null === $tournamentEntity) {
                // create Entity
                $tournamentEntity = new Tournament($tournament->name, $tournament->slug, $tournament->id);
                $tournamentEntity->setSportId($tournament->sport->id);
                $tournamentEntity->setCountryId($tournament->country->id);
                $this->entityManager->persist($tournamentEntity);
            } else {
                // update Entity
                $tournamentEntity->setName($tournament->name);
                $tournamentEntity->setSlug($tournament->slug);
            }
        }

        $this->entityManager->flush();
        

        echo 'Tournaments persisted successfully.', \PHP_EOL;
    }

    public function parseEvents(string $data) {

        $events = $this->serializer->deserialize($data, \App\DTO\Event::class.'[]', 'json');

            foreach ($events as $event) {
                // get Entity
                $eventEntity = $this->entityManager->getRepository(Event::class)->findOneBy(['external_id' => $event->id]);

                $homescore = empty($event->homeScore) ? null : $event->homeScore["total"];
                $awayscore = empty($event->awayScore) ? null : $event->awayScore["total"];

                if (null === $eventEntity) {

                    // create Entity
                    $eventEntity = new Event($event->slug, $event->startDate, $event->status, $event->winnerCode, $event->round, $event->id);

                    $eventEntity->setTournamentId($event->tournament->id);
                    $eventEntity->setHomeTeamId($event->homeTeam->id);
                    $eventEntity->setAwayTeamId($event->awayTeam->id);
                    $eventEntity->setHomeScore($homescore);
                    $eventEntity->setAwayScore($awayscore);

                    $this->entityManager->persist($eventEntity);

                } else {
                    // update Entity
                    $eventEntity->setSlug($event->slug);
                    $eventEntity->setStartDate($event->startDate);
                    $eventEntity->setStatus(EventStatusEnum::from($event->status) ?? EventStatusEnum::NotStarted);
                    $eventEntity->setWinnerCode($event->winnerCode);
                    $eventEntity->setHomeScore($homescore);
                    $eventEntity->setAwayScore($awayscore);
                }
            }

            $this->entityManager->flush();
        

        echo 'Events persisted successfully.', \PHP_EOL;
    }

    public function parseTeams(string $data) {

        $teams = $this->serializer->deserialize($data, \App\DTO\Team::class.'[]', 'json');
    
            foreach ($teams as $team) {
                // get Entity
                $teamEntity = $this->entityManager->getRepository(Team::class)->findOneBy(['external_id' => $team->id]);

                if (null === $teamEntity) {
                    // create Entity
                    $teamEntity = new Team($team->name, $team->managerName, $team->venue, $team->id);
                    $teamEntity->setCountryId($team->country->id);

                    $sport_id = $team->tournaments[0]["sport"]["id"];
                    $teamEntity->setSportId($sport_id);
                    
                    $this->entityManager->persist($teamEntity);
                } else {
                    // update Entity
                    $teamEntity->setName($team->name);
                    $teamEntity->setManagerName($team->managerName);
                    $teamEntity->setVenue($team->venue);
                }
            }

            $this->entityManager->flush();

        echo 'Teams persisted successfully.', \PHP_EOL;
    }

    public function parsePlayers(string $data) {
        $players = $this->serializer->deserialize($data, \App\DTO\Player::class.'[]', 'json');
    
            foreach ($players as $player) {
                // get Entity
                $playerEntity = $this->entityManager->getRepository(Player::class)->findOneBy(['external_id' => $player->id]);

                if (null === $playerEntity) {
                    // create Entity
                    $playerEntity = new Player($player->name, $player->slug, $player->position, $player->dateOfBirth, $player->id);
                    $playerEntity->setCountryId($player->country->id);
                    $playerEntity->setSportId($player->sport->id);
                    $playerEntity->setTeamId($player->team->id);
                    $this->entityManager->persist($playerEntity);
                } else {
                    // update Entity
                    $playerEntity->setName($player->name);
                    $playerEntity->setSlug($player->slug);
                    $playerEntity->setPosition($player->position);
                    $playerEntity->setDateOfBirth($player->dateOfBirth);
                }
            }

            $this->entityManager->flush();

        echo 'Players persisted successfully.', \PHP_EOL;
    }

    public function parseStandings(string $data) {
        $standings = $this->serializer->deserialize($data, \App\DTO\Standing::class.'[]', 'json');

        foreach ($standings as $standing) {
            $tournament_id = $standing->tournament->id;

            $sortedStandingsRows = $standing->sortedStandingsRows;
            foreach ($sortedStandingsRows as $sortedStandingsRow) {

                $standingEntity = $this->entityManager->getRepository(Standings::class)->findOneBy(['external_id' => $sortedStandingsRow["id"]]);

                if (null === $standingEntity) {
                    // create Entity
                    $standingEntity = new Standings($sortedStandingsRow["scoresFor"], $sortedStandingsRow["scoresAgainst"], $sortedStandingsRow["played"], $sortedStandingsRow["wins"], $sortedStandingsRow["draws"], $sortedStandingsRow["losses"], $sortedStandingsRow["percentage"], $sortedStandingsRow["id"]);
                    $standingEntity->setTournamentId($tournament_id);
                    $standingEntity->setTeamId($sortedStandingsRow["team"]["id"]);
                    $this->entityManager->persist($standingEntity);

                } else {
                    // update Entity
                    $standingEntity->setScoresFor($sortedStandingsRow["scoresFor"]);
                    $standingEntity->setScoresAgainst($sortedStandingsRow["scoresAgainst"]);
                    $standingEntity->setPlayed($sortedStandingsRow["played"]);
                    $standingEntity->setWins($sortedStandingsRow["wins"]);
                    $standingEntity->setLooses($sortedStandingsRow["losses"]);
                    $standingEntity->setDraws($sortedStandingsRow["draws"]);
                    $standingEntity->setPercentage($sortedStandingsRow["percentage"]);
                }
            }
        
        }

        $this->entityManager->flush();

        echo 'Standings persisted successfully.', \PHP_EOL;
    }

}