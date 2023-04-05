<?php

declare(strict_types=1);

namespace App\Tests;

use App\Kernel;

abstract class KernelTestCase
{
    public static function createKernel(): Kernel
    {
        $kernel = new Kernel();
        $kernel->boot();

        $kernel->getContainer()->setParameter('database.dsn', 'pgsql:host=localhost;port=5433;dbname=projekt-1_tests;user=postgres;password=nina');

        return $kernel;
    }
}
