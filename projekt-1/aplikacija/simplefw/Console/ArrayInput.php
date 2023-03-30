<?php

declare(strict_types=1);

namespace SimpleFW\Console;

use SimpleFW\Console\Exception\InvalidArgumentException;

final readonly class ArrayInput implements InputInterface
{
    public function __construct(
        private array $arguments = [],
        private array $options = [],
    ) {
    }

    public function getArgument(int $key): string
    {
        return $this->arguments[$key] ?? throw new InvalidArgumentException(sprintf('An argument with the key "%d" does not exist.', $key));
    }

    public function hasArgument(int $key): bool
    {
        return isset($this->arguments[$key]);
    }

    public function getOption(string $name): string
    {
        return $this->options[$name] ?? throw new InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
    }

    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }
}
