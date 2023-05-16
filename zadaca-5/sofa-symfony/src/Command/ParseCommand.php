<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Connection;
use App\Parser\JsonFeedParser;
use App\Parser\XmlFeedParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:parse',
    description: 'Parses the given file.',
)]
final class ParseCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
        private readonly JsonFeedParser $jsonFeedParser,
        private readonly XmlFeedParser $xmlFeedParser,
        #[Autowire('%kernel.project_dir%/data')]
        private readonly string $dataDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('filename', InputArgument::REQUIRED, 'The name of the file to be parsed.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->hasArgument('filename')) {
            $output->writeln('Required argument filename missing.');

            return self::FAILURE;
        }

        $filename = $input->getArgument('filename');
        $filepath = $this->dataDir.'/'.$filename;

        if (!file_exists($filepath)) {
            $output->writeln(sprintf('No file with the name "%s" was found.', $filename));

            return self::FAILURE;
        }

        try {
            $parser = match ($mediaType = mime_content_type($filepath)) {
                'application/json' => $this->jsonFeedParser,
                'text/xml' => $this->xmlFeedParser,
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
                'external_id' => $sport->id,
                'name' => $sport->name,
                'slug' => $sport->slug,
            ]);

            foreach ($sport->tournaments as $tournament) {
                $tournamentId = $this->connection->insert('tournament', [
                    'sport_id' => $sportId,
                    'external_id' => $tournament->id,
                    'name' => $tournament->name,
                    'slug' => $tournament->slug,
                ]);

                foreach ($tournament->events as $event) {
                    $this->connection->insert('event', [
                        'tournament_id' => $tournamentId,
                        'external_id' => $event->id,
                        'home_team_id' => $event->homeTeamId,
                        'away_team_id' => $event->awayTeamId,
                        'start_date' => $event->startDate->format(\DateTimeInterface::ATOM),
                        'home_score' => $event->homeScore,
                        'away_score' => $event->awayScore,
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
