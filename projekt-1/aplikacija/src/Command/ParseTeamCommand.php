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

        $sport = $parser->parse($content);

        try {
            $sportEntity = $this->entityManager->findOneBy(Sport::class, ['externalId' => $sport->externalId]);
            if ($sportEntity !== null) {
                $sportEntity->setName($sport->name);
                $sportEntity->setSlug($sport->slug);
            } else {
                $sportEntity = new Sport($sport->name, $sport->slug, $sport->externalId);
                $this->entityManager->persist($sportEntity);
            }

            foreach ($sport->getTeams() as $team) {

                $teamEntity = $this->entityManager->findOneBy(Team::class, ['externalId' => $team->externalId]);
                if ($teamEntity !== null) {
                    $teamEntity->setName($team->name);
                    $teamEntity->setSlug($team->slug);
                    $teamEntity->setSportId($sportEntity->getId());
                } else {
                    $teamEntity = new Team($team->name, $team->slug, $team->externalId);
                    $teamEntity->setSportId($sportEntity->getId());
                    $this->entityManager->persist($teamEntity);
                }
 
                foreach ($team->getPlayers() as $player) {
                    $playerEntity = $this->entityManager->findOneBy(Player::class, ['externalId' => $player->externalId]);
                    if ($playerEntity !== null) {
                        $playerEntity->setName($player->name);
                        $playerEntity->setSlug($player->slug);
                        $playerEntity->setTeamId($teamEntity->getId());
                    } else {
                        $playerEntity = new Player($player->name, $player->slug, $player->externalId);
                        $playerEntity->setTeamId($teamEntity->getId());
                        $this->entityManager->persist($playerEntity);
                    }
                }
            }

            $this->entityManager->flush();

            $output->writeln('The file was successfully parsed.');
        } catch (\PDOException $e) {
            
            $output->writeln(sprintf('The following error occurred: %s', $e->getMessage()));
            $output->writeln($e->getTraceAsString());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}