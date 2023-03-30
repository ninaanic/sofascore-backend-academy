<?php

declare(strict_types=1);

namespace App\Command;

use App\Tools\Slugger;
use SimpleFW\Console\CommandInterface;
use SimpleFW\Console\InputInterface;
use SimpleFW\Console\OutputInterface;

final class ExampleCommand implements CommandInterface
{
    public function __construct(
        private readonly Slugger $slugger,
    ) {
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->hasArgument(1)) {
            $output->writeln('Missing required arguments name.');

            return self::FAILURE;
        }

        $name = $input->getArgument(1);

        $output->writeln(sprintf('The slug for "%s" is "%s".', $name, $this->slugger->slugify($name)));

        return self::SUCCESS;
    }
}
