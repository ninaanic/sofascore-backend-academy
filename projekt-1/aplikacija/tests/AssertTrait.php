<?php

declare(strict_types=1);

namespace App\Tests;

trait AssertTrait
{
    public function assert($expected, $actual): void
    {
        echo sprintf('Asserting that "%s" is equal to "%s" in %s. ', json_encode($expected), json_encode($actual), $this->getFileAndLine());
        if ($expected !== $actual) {
            echo "\033[31mFAILED\033[0m", \PHP_EOL;
            throw new AssertionException();
        } else {
            echo "\033[32mSUCCESS\033[0m", \PHP_EOL;
        }
    }

    public function assertTrue($actual): void
    {
        $this->assert(true, $actual);
    }

    public function assertFalse($actual): void
    {
        $this->assert(false, $actual);
    }

    public function assertException(string $expectedException, callable $closure): void
    {
        echo sprintf('Asserting that an exception of type "%s" was thrown in %s. ', $expectedException, $this->getFileAndLine());

        try {
            $closure();
            echo "\033[31mFAILED, no exception was thrown\033[0m", \PHP_EOL;
            throw new AssertionException();
        } catch (\Throwable $e) {
            if ($expectedException !== $e::class) {
                echo sprintf("\033[31mFAILED, a \"%s\" exception was thrown\033[0m", $e::class), \PHP_EOL;
                throw new AssertionException();
            } else {
                echo "\033[32mSUCCESS\033[0m", \PHP_EOL;
            }
        }
    }

    private function getFileAndLine(): string
    {
        $fileAndLine = '';
        foreach (debug_backtrace(0, 5) as $trace) {
            if (__FILE__ !== $trace['file']) {
                $fileAndLine = $trace['file'].':'.$trace['line'];
                break;
            }
        }

        return $fileAndLine;
    }
}
