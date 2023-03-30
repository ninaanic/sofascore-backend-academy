<?php

declare(strict_types=1);

namespace SimpleFW\Console;

final class BufferedOutput implements OutputInterface
{
    private string $buffer = '';

    public function write(string $message): void
    {
        $this->buffer .= $message;
    }

    public function writeln(string $message): void
    {
        $this->write($message.\PHP_EOL);
    }

    public function fetch(): string
    {
        try {
            return $this->buffer;
        } finally {
            $this->buffer = '';
        }
    }
}
