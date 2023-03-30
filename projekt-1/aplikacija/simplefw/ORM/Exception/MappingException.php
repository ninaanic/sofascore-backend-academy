<?php

declare(strict_types=1);

namespace SimpleFW\ORM\Exception;

class MappingException extends \RuntimeException implements ORMExceptionInterface
{
    public static function becauseClassIsNotAnEntity(string $class): self
    {
        return new self(sprintf('"%s" is not an entity.', $class));
    }

    public static function becauseIdIsNotSet(string $class): self
    {
        return new self(sprintf('The entity "%s" does not have an id.', $class));
    }
}
