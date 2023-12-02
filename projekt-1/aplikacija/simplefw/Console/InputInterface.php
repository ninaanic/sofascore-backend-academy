<?php

declare(strict_types=1);

namespace SimpleFW\Console;

interface InputInterface
{
    public function getArgument(int $key): string;

    public function hasArgument(int $key): bool;

    public function getOption(string $name): string;

    public function hasOption(string $name): bool;
}
