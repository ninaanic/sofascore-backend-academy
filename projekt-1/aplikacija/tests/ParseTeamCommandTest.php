<?php

declare(strict_types=1);

namespace App\Tests;

use App\Command\ParseTeamCommand;
use SimpleFW\Console\ArrayInput;
use SimpleFW\Console\BufferedOutput;

final class ParseTeamCommandTest extends KernelTestCase
{
    use AssertTrait;

    public function run(): void
    {
        $kernel = self::createKernel();
        $exampleCommand = $kernel->getContainer()->get(ParseTeamCommand::class);

        $output = new BufferedOutput();

        $this->assert(1, $exampleCommand->execute(new ArrayInput(['app:parse:team']), $output));
        $this->assert('Required argument filename missing.'.\PHP_EOL, $output->fetch());

        $this->assert(0, $exampleCommand->execute(new ArrayInput(['app:parse:team', 'football-teams.json']), $output));
        $this->assert('The file was successfully parsed.'.\PHP_EOL, $output->fetch());

        $this->assert(0, $exampleCommand->execute(new ArrayInput(['app:parse:team', 'basketball-teams.xml']), $output));
        $this->assert('The file was successfully parsed.'.\PHP_EOL, $output->fetch());
    }
}
