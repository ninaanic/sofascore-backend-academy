<?php

declare(strict_types=1);

namespace App\Command;
use App\Database\Connection;
use App\Entity\Event;
use App\Entity\EventStatusEnum;
use App\Entity\Player;
use App\Entity\Standings;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Handler\ParseSportFileHandler;
use App\Message\ParseFile;
use App\Message\ProcessDataMessage;
use Doctrine\DBAL\ArrayParameters\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AsCommand(
    name: 'app:calculate:standings',
    description: 'Calculate standings for football.',
)]
final class CalculateStandingsCommand extends Command
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();    
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $tournaments = $this->entityManager->getRepository(Tournament::class)->findBy(['sport_id' => 1]);

            foreach($tournaments as $tournament) {
                $tournamentId = $tournament->getExternalId();
                $teams = $this->entityManager->getRepository(Team::class)->findBy(['sport_id' => 1]);

                foreach($teams as $team) {
                    $teamId = $team->getExternalId();
                    //var_dump($teamId);
                    
                    $home_events = $this->entityManager->getRepository(Event::class)->findBy(['tournament_id' => $tournamentId, 'status' => 'finished', 'home_team_id' => $teamId]);
                    $away_events = $this->entityManager->getRepository(Event::class)->findBy(['tournament_id' => $tournamentId, 'status' => 'finished', 'away_team_id' => $teamId]);

                    if ($home_events !== [] && $away_events !== []) {
                        $no_home_events = count($home_events);
                        $no_away_events = count($away_events);
                        
                        $played = $no_home_events + $no_away_events;
                        $wins = 0;
                        $looses = 0;
                        $draws = 0;
                        $scores_for = 0;
                        $scores_agains = 0;
                        $points = 0;

                        foreach($home_events as $home_event) {
                            $home_score = $home_event->getHomeScore();
                            $away_score = $home_event->getAwayScore();

                            if (isset($home_score) && isset($away_score)) {
                                $scores_for += $home_score;
                                $scores_agains += $away_score;
                                if ($home_score > $away_score) {
                                    $wins += 1;
                                    $points += 3;
                                } elseif ($home_score < $away_score) {
                                    $looses += 1;
                                } else {
                                    $draws += 1;
                                    $points += 1;
                                }
                            }
                        }

                        foreach($away_events as $away_event) {
                            $home_score = $away_event->getHomeScore();
                            $away_score = $away_event->getAwayScore();

                            if (isset($home_score) && isset($away_score)) {
                                $scores_for += $away_score;
                                $scores_agains += $home_score;
                                if ($home_score < $away_score) {
                                    $wins += 1;
                                    $points += 3;
                                } elseif ($home_score > $away_score) {
                                    $looses += 1;
                                } else {
                                    $draws += 1;
                                    $points += 1;
                                }
                            }
                        }

                        $standing = $this->entityManager->getRepository(Standings::class)->findOneBy(['tournament_id' => $tournamentId, 'team_id' => $teamId]);
                        $percentage = $wins / $played;

                        if ($standing !== null) {
                            //$standing->setPosition(0);
                            $standing->setPlayed($played);
                            $standing->setWins($wins);
                            $standing->setLooses($looses);
                            $standing->setDraws($draws);
                            $standing->setScoresFor($scores_for);
                            $standing->setScoresAgainst($scores_agains);
                            $standing->setPoints($points);
                            $standing->setPercentage($percentage);
                            $standing->setExternalId(null);
                        } else {
                            $standing = new Standings($scores_for, $scores_agains, $played, $wins, $draws, $looses, $percentage, null);
                            $standing->setPoints($points);
                            $standing->setTournamentId($tournamentId);
                            $standing->setTeamId($teamId);
                        }
                        $this->entityManager->persist($standing);
                        $this->entityManager->flush();
                    }
                }
            }

            echo 'The standings table for football was successfully created/updated.';

        } catch (\PDOException $e) {
            $output->writeln(sprintf('The following error occurred: %s', $e->getMessage()));
            $output->writeln($e->getTraceAsString());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}