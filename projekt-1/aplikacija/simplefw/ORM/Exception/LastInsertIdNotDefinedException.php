<?php

declare(strict_types=1);

namespace SimpleFW\ORM\Exception;

class LastInsertIdNotDefinedException extends \RuntimeException implements ORMExceptionInterface
{
    public function __construct(\PDOException $exception)
    {
        parent::__construct($exception->getMessage(), 0, $exception);
    }
}
