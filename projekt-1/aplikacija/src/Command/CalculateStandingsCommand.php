<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Event;
use App\Entity\Sport;
use App\Entity\Standings;
use App\Entity\Team;
use App\Entity\Tournament;
use SimpleFW\Console\CommandInterface;
use SimpleFW\Console\InputInterface;
use SimpleFW\Console\OutputInterface;
use SimpleFW\ORM\EntityManager;

final class CalculateStandingsCommand implements CommandInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $tournaments = $this->entityManager->findBy(Tournament::class, []);

            foreach($tournaments as $tournament) {
                $tournamentId = $tournament->getId();
                $teams = $this->entityManager->findBy(Team::class, ['sportId' => $tournament->getSportId()]);

                foreach($teams as $team) {
                    $teamId = $team->getId();
                    
                    $home_events = $this->entityManager->findBy(Event::class, ['tournamentId' => $tournamentId, 'status' => 'finished', 'homeTeamId' => $teamId]);
                    $away_events = $this->entityManager->findBy(Event::class, ['tournamentId' => $tournamentId, 'status' => 'finished', 'awayTeamId' => $teamId]);

                    if ($home_events !== [] && $away_events !== []) {
                        $no_home_events = count($home_events);
                        $no_away_events = count($away_events);
                        
                        $matches = $no_home_events + $no_away_events;
                        $wins = 0;
                        $looses = 0;
                        $draws = 0;
                        $scores_for = 0;
                        $scores_agains = 0;
                        $points = 0;

                        $sports = $this->entityManager->findBy(Sport::class, ['id' => $tournament->getSportId()]);
                        foreach($sports as $sport) {
                            if ($sport->getSlug() === 'football') {
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
                            } elseif ($sport->getSlug() === 'basketball') {
                                foreach($home_events as $home_event) {
                                    $home_score = $home_event->getHomeScore();
                                    $away_score = $home_event->getAwayScore();
        
                                    if (isset($home_score) && isset($away_score)) {
                                        $scores_for += $home_score;
                                        $scores_agains += $away_score;
                                        if ($home_score > $away_score) {
                                            $wins += 1;
                                            $points += 2;
                                        } elseif ($home_score < $away_score) {
                                            $looses += 1;
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
                                            $points += 2;
                                        } elseif ($home_score > $away_score) {
                                            $looses += 1;
                                            $points += 1;
                                        } 
                                    }
                                }
                            }
                        }

                        $standing = $this->entityManager->findOneBy(Standings::class, ['tournamentId' => $tournamentId, 'teamId' => $teamId]);

                        if ($standing !== null) {
                            $standing->setPosition(0);
                            $standing->setMatches($matches);
                            $standing->setWins($wins);
                            $standing->setLooses($looses);
                            $standing->setDraws($draws);
                            $standing->setScoresFor($scores_for);
                            $standing->setScoresAgainst($scores_agains);
                            $standing->setPoints($points);
                        } else {
                            $standing = new Standings(
                                0, $matches, $wins, $looses, $draws, $scores_for, $scores_agains, $points
                            );
                            $standing->setTournamentId($tournamentId);
                            $standing->setTeamId($teamId);
                        }
                        $this->entityManager->persist($standing);
                        $this->entityManager->flush();

                        // odredivanje pozicija (positions) po broju bodova (points)
                        $standings = $this->entityManager->findBy(Standings::class, ['tournamentId' => $tournamentId]);
                        $points = array();
                        foreach($standings as $key => $val) {
                            $points[$key] = $val->getPoints();
                        }
                        array_multisort($points, SORT_DESC, $standings);
                        
                        foreach($standings as $standing) {
                            $position = array_search($standing, $standings) + 1;
                            $standing->setPosition($position);
                            $this->entityManager->persist($standing);
                            $this->entityManager->flush();
                        }
                    }
                }
            }

            $output->writeln('The standings table was successfully created/updated.');

        } catch (\PDOException $e) {
            $output->writeln(sprintf('The following error occurred: %s', $e->getMessage()));
            $output->writeln($e->getTraceAsString());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
