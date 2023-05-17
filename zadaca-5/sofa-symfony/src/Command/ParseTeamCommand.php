<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Connection;
use App\Entity\Team;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:parse:team',
    description: 'Parses the given json file of team data.',
)]
final class ParseTeamCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
        #[Autowire('%kernel.project_dir%/data')]
        private readonly string $dataDir,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
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

        if (false === $content = @file_get_contents($filepath)) {
            $output->writeln(sprintf('Unable to read the file "%s".', $filename));

            return self::FAILURE;
        }

        $teams = $this->serializer->deserialize($content, \App\DTO\Team::class.'[]', 'json');

        foreach ($teams as $team) {
            $teamEntity = $this->entityManager->getRepository(Team::class)->findOneBy(['externalId' => $team->id]);;
            if (null === $teamEntity) {
                $teamEntity = new Team($team->name, $team->id);
                $this->entityManager->persist($teamEntity);
            } else {
                $teamEntity->setName($team->name);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Teams persisted successfully.');

        return Command::SUCCESS;
    }
}