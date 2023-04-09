<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Connection;
use App\Parser\JsonTeamParser;
use App\Parser\XmlTeamParser;
use SimpleFW\Console\CommandInterface;
use SimpleFW\Console\InputInterface;
use SimpleFW\Console\OutputInterface;
use SimpleFW\ORM\EntityManager;

final class ParseTeamCommand implements CommandInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly JsonTeamParser $jsonTeamParser,
        private readonly XmlTeamParser $xmlTeamParser,
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
                'json' => $this->jsonTeamParser,
                'xml' => $this->xmlTeamParser,
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

            foreach ($sport->teams as $team) {

                $existingTeam = $this->connection->findOne('team', [], ['external_id' => $team->externalId]);
                if ($existingTeam !== null) {
                    $teamId = $existingTeam['id'];
                    $this->connection->update('team', [
                        'name' => $team->name,
                        'slug' => $team->slug,
                        'external_id' => $team->externalId,
                        'sport_id' => $sportId,
                    ], $teamId);
                } else {
                    $teamId = $this->connection->insert('team', [
                        'name' => $team->name,
                        'slug' => $team->slug,
                        'external_id' => $team->externalId,
                        'sport_id' => $sportId,
                    ]);
                }
 
                foreach ($team->players as $player) {
                    $existingPlayer = $this->connection->findOne('player', [], ['external_id' => $player->externalId]);
                    if ($existingPlayer !== null) {
                        $playerId = $existingPlayer['id'];
                        $this->connection->update('player', [
                            'name' => $player->name,
                            'slug' => $player->slug,
                            'external_id' => $player->externalId,
                            'team_id' => $teamId,
                        ], $playerId);
                    } else {
                        $this->connection->insert('player', [
                            'name' => $player->name,
                            'slug' => $player->slug,
                            'external_id' => $player->externalId,
                            'team_id' => $teamId,
                        ]);
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