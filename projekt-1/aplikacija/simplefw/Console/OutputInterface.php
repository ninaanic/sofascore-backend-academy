<?php

declare(strict_types=1);

namespace SimpleFW\Console;

interface OutputInterface
{
    public function write(string $message): void;

    public function writeln(string $message): void;
}
