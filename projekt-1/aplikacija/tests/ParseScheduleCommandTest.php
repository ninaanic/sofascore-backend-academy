<?php

declare(strict_types=1);

namespace App\Tests;

use App\Command\ParseScheduleCommand;
use SimpleFW\Console\ArrayInput;
use SimpleFW\Console\BufferedOutput;

final class ParseScheduleCommandTest extends KernelTestCase
{
    use AssertTrait;

    public function run(): void
    {
        $kernel = self::createKernel();
        $exampleCommand = $kernel->getContainer()->get(ParseScheduleCommand::class);

        $output = new BufferedOutput();

        $this->assert(1, $exampleCommand->execute(new ArrayInput(['app:parse:schedule']), $output));
        $this->assert('Required argument filename missing.'.\PHP_EOL, $output->fetch());

        $this->assert(0, $exampleCommand->execute(new ArrayInput(['app:parse:schedule', 'football-schedule.json']), $output));
        $this->assert('The file was successfully parsed.'.\PHP_EOL, $output->fetch());

        $this->assert(0, $exampleCommand->execute(new ArrayInput(['app:parse:schedule', 'basketball-schedule.xml']), $output));
        $this->assert('The file was successfully parsed.'.\PHP_EOL, $output->fetch());
    }
}
