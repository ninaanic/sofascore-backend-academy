<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Connection;
use SimpleFW\Console\CommandInterface;
use SimpleFW\Console\InputInterface;
use SimpleFW\Console\OutputInterface;

final class CalculateStandingsCommand implements CommandInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->connection->startTransaction();

            $tournaments = $this->connection->find('tournament');

            foreach($tournaments as $tournament) {
                $tournamentId = $tournament['id'];
                $teams = $this->connection->find('team', [], ['sport_id' => $tournament['sport_id']]);

                foreach($teams as $team) {
                    $teamId = $team['id'];
                    
                    $home_events = $this->connection->find('event', [], ['tournament_id' => $tournamentId, 'status' => 'finished', 'home_team_id' => $teamId]);
                    $away_events = $this->connection->find('event', [], ['tournament_id' => $tournamentId, 'status' => 'finished', 'away_team_id' => $teamId]);

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

                        $sports = $this->connection->find('sport', [], ['id' => $tournament['sport_id']]);
                        foreach($sports as $sport) {
                            if ($sport['slug'] === 'football') {
                                foreach($home_events as $home_event) {
                                    $home_score = $home_event['home_score'];
                                    $away_score = $home_event['away_score'];
        
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
                                    $home_score = $away_event['home_score'];
                                    $away_score = $away_event['away_score'];
        
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
                            } elseif ($sport['slug'] === 'basketball') {
                                foreach($home_events as $home_event) {
                                    $home_score = $home_event['home_score'];
                                    $away_score = $home_event['away_score'];
        
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
                                    $home_score = $away_event['home_score'];
                                    $away_score = $away_event['away_score'];
        
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

                        $existingStanding = $this->connection->findOne('standings', [], ['tournament_id' => $tournamentId, 'team_id' => $teamId]);

                        if ($existingStanding !== null) {
                            $standingId = $existingStanding['id'];
                            $this->connection->update('standings', [
                                'position' => 0,
                                'matches' => $matches,
                                'wins' => $wins,
                                'looses' => $looses,
                                'draws' => $draws,
                                'scores_for' => $scores_for,
                                'scores_against' => $scores_agains,
                                'points' => $points,
                                'tournament_id' => $tournamentId,
                                'team_id' => $teamId,
                            ], $standingId);
                        } else {
                            $this->connection->insert('standings', [
                                'position' => 0,
                                'matches' => $matches,
                                'wins' => $wins,
                                'looses' => $looses,
                                'draws' => $draws,
                                'scores_for' => $scores_for,
                                'scores_against' => $scores_agains,
                                'points' => $points,
                                'tournament_id' => $tournamentId,
                                'team_id' => $teamId,
                            ]);
                        }

                        // odredivanje pozicija (positions) po broju bodova (points)
                        $standings = $this->connection->find('standings', [], ['tournament_id' => $tournamentId]);
                        $points = array();
                        foreach($standings as $key => $val) {
                            $points[$key] = $val['points'];
                        }
                        array_multisort($points, SORT_DESC, $standings);
                        
                        foreach($standings as $standing) {
                            $standingId = $standing['id'];
                            $position = array_search($standing, $standings) + 1;
                            $this->connection->update('standings', ['position' => $position], $standingId);
                        }
                    }
                }
            }

            $this->connection->commit();

            $output->writeln('The standings table was successfully created/updated.');

        } catch (\PDOException $e) {
            $this->connection->rollback();

            $output->writeln(sprintf('The following error occurred: %s', $e->getMessage()));
            $output->writeln($e->getTraceAsString());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
