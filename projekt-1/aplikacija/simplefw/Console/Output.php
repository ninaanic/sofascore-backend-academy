<?php

declare(strict_types=1);

namespace SimpleFW\Console;

final class Output implements OutputInterface
{
    public function write(string $message): void
    {
        echo $message;
    }

    public function writeln(string $message): void
    {
        $this->write($message.\PHP_EOL);
    }
}
