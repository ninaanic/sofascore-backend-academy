<?php

declare(strict_types=1);

namespace App\Tests;

use App\Command\ExampleCommand;
use SimpleFW\Console\ArrayInput;
use SimpleFW\Console\BufferedOutput;

final class ExampleCommandTest extends KernelTestCase
{
    use AssertTrait;

    public function run(): void
    {
        $kernel = self::createKernel();
        $exampleCommand = $kernel->getContainer()->get(ExampleCommand::class);

        $output = new BufferedOutput();

        $this->assert(1, $exampleCommand->execute(new ArrayInput(['app:example']), $output));
        $this->assert('Missing required arguments name.'.\PHP_EOL, $output->fetch());

        $this->assert(0, $exampleCommand->execute(new ArrayInput(['app:example', 'Some string with čćšđž']), $output));
        $this->assert('The slug for "Some string with čćšđž" is "some-string-with-ccsdz".'.\PHP_EOL, $output->fetch());
    }
}
