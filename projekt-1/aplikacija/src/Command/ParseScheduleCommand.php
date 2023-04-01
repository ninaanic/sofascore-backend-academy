<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Connection;
use App\Parser\JsonScheduleParser;
use App\Parser\XmlScheduleParser;
use SimpleFW\Console\CommandInterface;
use SimpleFW\Console\Input;
use SimpleFW\Console\Output;

final class ParseScheduleCommand implements CommandInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly JsonScheduleParser $jsonScheduleParser,
        private readonly XmlScheduleParser $xmlScheduleParser,
        private readonly string $projectDir,
    ) {
    }

    public function execute(Input $input, Output $output): int
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
            // todo promijenit da gleda ekstenziju
            $parser = match ($mediaType = mime_content_type($filepath)) {
                'application/json' => $this->jsonScheduleParser,
                'text/xml' => $this->xmlScheduleParser,
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

            $sportId = $this->connection->insert('sport', [
                'name' => $sport->name,
                'slug' => $sport->slug,
                'external_id' => $sport->externalId,
            ]);

            foreach ($sport->tournaments as $tournament) {
                $tournamentId = $this->connection->insert('tournament', [
                    'name' => $tournament->name,
                    'slug' => $tournament->slug,
                    'external_id' => $tournament->externalId,
                    'sport_id' => $sportId,
                ]);

                // todo ako podatci već postoje treba ih ažurirati 
                foreach ($tournament->events as $event) {
                    $this->connection->insert('event', [
                        'slug' => $event->slug,
                        'status' => $event->status,
                        'home_score' => $event->homeScore,
                        'away_score' => $event->awayScore,
                        'start_date' => $event->startDate->format(\DateTimeInterface::ATOM),
                        'external_id' => $event->externalId,
                        'home_team_id' => $event->homeTeamId,
                        'away_team_id' => $event->awayTeamId,
                        'tournament_id' => $tournamentId,
                    ]);
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