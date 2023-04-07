<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Connection;
use App\Parser\JsonScheduleParser;
use App\Parser\XmlScheduleParser;
use SimpleFW\Console\CommandInterface;
use SimpleFW\Console\Input;
use SimpleFW\Console\InputInterface;
use SimpleFW\Console\Output;
use SimpleFW\Console\OutputInterface;

final class ParseScheduleCommand implements CommandInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly JsonScheduleParser $jsonScheduleParser,
        private readonly XmlScheduleParser $xmlScheduleParser,
        private readonly string $projectDir,
    ) {
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->hasArgument(1)) {
            $output->writeln('Required argument filename missing.');

            return self::FAILURE;
        }

        $filename = $input->getArgument(1);
        $filepath = $this->projectDir.'/data/'.$filename;

        if (!file_exists($filepath)) {
            $output->writeln(sprintf('No file with the name "%s" was found.', $filename));

            return self::FAILURE;
        }

        try {
            $parser = match ($mediaType = pathinfo($filename, PATHINFO_EXTENSION)) {
                'json' => $this->jsonScheduleParser,
                'xml' => $this->xmlScheduleParser,
            };
        } catch (\UnhandledMatchError) {
            $output->writeln(sprintf('The file "%s" has an unknown media type "%s".', $filename, $mediaType));

            return self::FAILURE;
        }

        if (false === $content = @file_get_contents($filepath)) {
            $output->writeln(sprintf('Unable to read the file "%s".', $filename));

            return self::FAILURE;
        }

        $sport = $parser->parse($content);

        try {
            $this->connection->startTransaction();

            $existingSport = $this->connection->findOne('sport', [], ['external_id' => $sport->externalId]);
            if ($existingSport !== null) {
                $sportId = $existingSport['id'];
                $this->connection->update('sport', [
                    'name' => $sport->name,
                    'slug' => $sport->slug,
                    'external_id' => $sport->externalId,
                ], $sportId);
            } else {
                $sportId = $this->connection->insert('sport', [
                    'name' => $sport->name,
                    'slug' => $sport->slug,
                    'external_id' => $sport->externalId,
                ]);
            }

            foreach ($sport->tournaments as $tournament) {
                $existingTournament = $this->connection->findOne('tournament', [], ['external_id' => $tournament->externalId]);
                if ($existingTournament !== null) {
                    $tournamentId = $existingTournament['id'];
                    $this->connection->update('tournament', [
                        'name' => $tournament->name,
                        'slug' => $tournament->slug,
                        'external_id' => $tournament->externalId,
                        'sport_id' => $sportId,
                    ], $tournamentId);
                } else {
                    $tournamentId = $this->connection->insert('tournament', [
                        'name' => $tournament->name,
                        'slug' => $tournament->slug,
                        'external_id' => $tournament->externalId,
                        'sport_id' => $sportId,
                    ]);
                }

                foreach ($tournament->events as $event) {
                    $existingEvent = $this->connection->findOne('event', [], ['external_id' => $event->externalId]);
                    $HomeTeam = $this->connection->findOne('team', [], ['external_id' => $event->homeTeamId]);
                    $AwayTeam = $this->connection->findOne('team', [], ['external_id' => $event->awayTeamId]);

                    if ($existingEvent !== null) {
                        if ($HomeTeam !== null && $AwayTeam !== null) {
                            $HomeTeamId = $HomeTeam['id'];
                            $AwayTeamId = $AwayTeam['id'];
                            $eventId = $existingEvent['id'];

                            $string_to_hash = $tournamentId . $HomeTeamId . $AwayTeamId . $event->startDate->format(\DateTimeInterface::ATOM);
                            $slug = hash('sha256', $string_to_hash);

                            $this->connection->update('event', [
                                'slug' => $slug,
                                'home_score' => isset($event->homeScore) ? $event->homeScore : null,
                                'away_score' => isset($event->awayScore) ? $event->awayScore : null,
                                'start_date' => $event->startDate->format(\DateTimeInterface::ATOM),
                                'external_id' => $event->externalId,
                                'home_team_id' => $HomeTeamId,
                                'away_team_id' => $AwayTeamId,
                                'tournament_id' => $tournamentId,
                                'status' => $event->status,
                            ], $eventId);
                        }
                    } else {
                        if ($HomeTeam !== null && $AwayTeam !== null) {
                            $HomeTeamId = $HomeTeam['id'];
                            $AwayTeamId = $AwayTeam['id'];

                            $string_to_hash = $tournamentId . $HomeTeamId . $AwayTeamId . $event->startDate->format(\DateTimeInterface::ATOM);
                            $slug = hash('sha256', $string_to_hash);

                            $this->connection->insert('event', [
                                'slug' => $slug,
                                'home_score' => isset($event->homeScore) ? $event->homeScore : null,
                                'away_score' => isset($event->awayScore) ? $event->awayScore : null,
                                'start_date' => $event->startDate->format(\DateTimeInterface::ATOM),
                                'external_id' => $event->externalId,
                                'home_team_id' => $HomeTeamId,
                                'away_team_id' => $AwayTeamId,
                                'tournament_id' => $tournamentId,
                                'status' => $event->status,
                            ]);
                        }
                    }
                }
            }

            $this->connection->commit();

            $output->writeln('The file was successfully parsed.');
        } catch (\PDOException $e) {
            $this->connection->rollback();

            $output->writeln(sprintf('The following error occurred: %s', $e->getMessage()));
            $output->writeln($e->getTraceAsString());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}