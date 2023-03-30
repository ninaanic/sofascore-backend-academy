<?php

declare(strict_types=1);

namespace SimpleFW\ORM;

final readonly class ClassMetadata
{
    /**
     * @param list<string, \ReflectionProperty> $properties
     * @param list<string, string>              $columns
     */
    public function __construct(
        public \ReflectionClass $reflection,
        public array $properties,
        public string $table,
        public array $columns,
        public string $id,
    ) {
    }

    public function idReflection(): \ReflectionProperty
    {
        return $this->properties[$this->id];
    }

    public function idColumn(): string
    {
        return $this->columns[$this->id];
    }
}
