<?php

declare(strict_types=1);

namespace App\Command;

use App\Tools\Slugger;
use SimpleFW\Console\CommandInterface;
use SimpleFW\Console\Input;
use SimpleFW\Console\Output;

final class ParseCommand implements CommandInterface
{
    public function __construct(
        private readonly Slugger $slugger,
    ) {
    }

    public function execute()
    {
        // TODO kod iz parse.php stavit tu
    }
}
