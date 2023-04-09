<?php

declare(strict_types=1);

namespace App\Tests;

use App\Command\CalculateStandingsCommand;
use SimpleFW\Console\ArrayInput;
use SimpleFW\Console\BufferedOutput;

final class CalculateStandingsCommandTest extends KernelTestCase
{
    use AssertTrait;

    public function run(): void
    {
        $kernel = self::createKernel();
        $exampleCommand = $kernel->getContainer()->get(CalculateStandingsCommand::class);

        $output = new BufferedOutput();

        $this->assert(0, $exampleCommand->execute(new ArrayInput(['app:parse:schedule']), $output));
        $this->assert('The standings table was successfully created/updated.'.\PHP_EOL, $output->fetch());
    }
}