<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Player;
use App\Entity\Sport;
use App\Entity\Team;
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

        try {
            $parser->parse($content);
            $output->writeln('The file was successfully parsed.');
        } catch (\PDOException $e) {
            $output->writeln(sprintf('The following error occurred: %s', $e->getMessage()));
            $output->writeln($e->getTraceAsString());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}