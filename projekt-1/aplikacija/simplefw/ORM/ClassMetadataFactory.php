<?php

declare(strict_types=1);

namespace SimpleFW\ORM;

use SimpleFW\ORM\Attribute\Column;
use SimpleFW\ORM\Attribute\Entity;
use SimpleFW\ORM\Attribute\Id;
use SimpleFW\ORM\Exception\MappingException;

final class ClassMetadataFactory
{
    /**
     * @var array<class-string, ClassMetadata>
     */
    private array $metadata = [];

    /**
     * @param class-string $class
     */
    public function getMetadata(string $class): ClassMetadata
    {
        if (isset($this->metadata[$class])) {
            return $this->metadata[$class];
        }

        $reflectionClass = new \ReflectionClass($class);

        /** @var \ReflectionAttribute<Entity> $reflectionAttribute */
        $reflectionAttribute = $reflectionClass->getAttributes(Entity::class)[0] ?? null;

        if (null === $reflectionAttribute) {
            throw MappingException::becauseClassIsNotAnEntity($class);
        }

        $table = $reflectionAttribute->newInstance()->tableName ?? $this->toSnakeCase($reflectionClass->getShortName());

        $properties = [];
        $id = null;
        $columns = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $reflectionAttribute = $reflectionProperty->getAttributes(Column::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;

            if (null === $reflectionAttribute) {
                continue;
            }

            $properties[$reflectionProperty->name] = $reflectionProperty;

            $columnAttribute = $reflectionAttribute->newInstance();

            if ($columnAttribute instanceof Id) {
                $id = $reflectionProperty->name;
            }

            $columns[$reflectionProperty->name] = $columnAttribute->name ?? $this->toSnakeCase($reflectionProperty->name);
        }

        if (null === $id) {
            throw MappingException::becauseIdIsNotSet($class);
        }

        return $this->metadata[$class] = new ClassMetadata($reflectionClass, $properties, $table, $columns, $id);
    }

    private function toSnakeCase(string $name): string
    {
        return strtolower(preg_replace('~[A-Z]~', '_\\0', lcfirst($name)));
    }
}
