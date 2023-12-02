<?php

declare(strict_types=1);

namespace SimpleFW\ORM;

use SimpleFW\ORM\Exception\ORMExceptionInterface;

final class EntityManager
{
    private const STATE_MANAGED = 1;
    private const STATE_NEW = 2;
    private const STATE_REMOVED = 3;

    private array $entities = [];
    private array $entityIds = [];
    private array $rawData = [];
    private array $entityStates = [];
    private array $entityChangeSets = [];
    private array $entityUpdates = [];
    private array $entityInsertions = [];
    private array $entityDeletions = [];

    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private readonly ClassMetadataFactory $classMetadataFactory,
    ) {
    }

    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public function find(string $class, mixed $id): ?object
    {
        if (isset($this->entityIds[$class][$id])) {
            return $this->entities[$this->entityIds[$class][$id]];
        }

        $metadata = $this->classMetadataFactory->getMetadata($class);

        return $this->findOneBy($class, [
            $metadata->idColumn() => $id,
        ]);
    }

    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public function findOneBy(string $class, array $criteria, ?array $orderBy = null): ?object
    {
        $result = $this->findBy($class, $criteria, $orderBy, 1);

        return $result[0] ?? null;
    }

    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return list<T>
     */
    public function findBy(string $class, array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $metadata = $this->classMetadataFactory->getMetadata($class);

        $params = [];
        foreach ($criteria as $propertyName => $value) {
            settype($value, $metadata->properties[$propertyName]->getType()->getName());
            $params[$metadata->columns[$propertyName]] = $value;
        }

        $orderByColumns = [];
        foreach ($orderBy ?? [] as $propertyName => $direction) {
            $orderByColumns[$metadata->columns[$propertyName]] = $direction;
        }

        $result = $this->queryBuilder->find($metadata->table, array_values($metadata->columns), $params, $orderByColumns, $limit, $offset);

        $entities = [];
        foreach ($result as $rawData) {
            $id = $rawData[$metadata->idColumn()];

            if (isset($this->entityIds[$class][$id])) {
                $entity = $this->entities[$this->entityIds[$class][$id]];
            } else {
                $entity = $metadata->reflection->newInstanceWithoutConstructor();

                foreach ($metadata->properties as $propertyName => $reflectionProperty) {
                    $column = $metadata->columns[$propertyName];

                    settype($rawData[$column], $reflectionProperty->getType()->getName());
                    $reflectionProperty->setValue($entity, $rawData[$column]);
                }

                $hash = spl_object_hash($entity);
                $this->entities[$hash] = $entity;
                $this->entityIds[$class][$id] = $hash;
                $this->entityStates[$hash] = self::STATE_MANAGED;
                $this->rawData[$hash] = $rawData;
            }

            $entities[] = $entity;
        }

        return $entities;
    }

    public function persist(object $entity): void
    {
        $hash = spl_object_hash($entity);

        if (isset($this->entityStates[$hash]) && self::STATE_REMOVED !== $this->entityStates[$hash]) {
            return;
        }

        if (isset($this->entityStates[$hash]) && self::STATE_REMOVED === $this->entityStates[$hash]) {
            $this->entityStates[$hash] = self::STATE_MANAGED;
            unset($this->entityDeletions[$hash]);

            return;
        }

        $this->entityInsertions[$hash] = $entity;
        $this->entityStates[$hash] = self::STATE_NEW;
    }

    public function remove(object $entity): void
    {
        $hash = spl_object_hash($entity);

        if (!isset($this->entityStates[$hash]) || self::STATE_REMOVED === $this->entityStates[$hash]) {
            return;
        }

        if (self::STATE_NEW === $this->entityStates[$hash]) {
            unset(
                $this->entityInsertions[$hash],
                $this->entityStates[$hash],
            );

            return;
        }

        $this->entityStates[$hash] = self::STATE_REMOVED;
        $this->entityDeletions[$hash] = $entity;
    }

    public function flush(): void
    {
        $this->computeChangeSets();

        $entities = $this->entities;
        $entityIds = $this->entityIds;
        $rawData = $this->rawData;
        $entityStates = $this->entityStates;
        $entityChangeSets = $this->entityChangeSets;
        $entityUpdates = $this->entityUpdates;
        $entityInsertions = $this->entityInsertions;
        $entityDeletions = $this->entityDeletions;

        try {
            $this->queryBuilder->startTransaction();

            $this->executeInserts();
            $this->executeUpdates();
            $this->executeDeletes();

            $this->queryBuilder->commit();
        } catch (ORMExceptionInterface $e) {
            $this->entities = $entities;
            $this->entityIds = $entityIds;
            $this->rawData = $rawData;
            $this->entityStates = $entityStates;
            $this->entityChangeSets = $entityChangeSets;
            $this->entityUpdates = $entityUpdates;
            $this->entityInsertions = $entityInsertions;
            $this->entityDeletions = $entityDeletions;

            $this->queryBuilder->rollback();

            throw $e;
        }
    }

    public function clear(): void
    {
        $this->entities = [];
        $this->entityIds = [];
        $this->rawData = [];
        $this->entityStates = [];
        $this->entityChangeSets = [];
        $this->entityUpdates = [];
        $this->entityInsertions = [];
        $this->entityDeletions = [];
    }

    public function computeChangeSets(): void
    {
        $this->entityUpdates = [];
        $this->entityChangeSets = [];

        foreach ($this->entities as $hash => $entity) {
            $metadata = $this->classMetadataFactory->getMetadata($entity::class);

            $changeSet = [];
            foreach ($metadata->properties as $propertyName => $reflectionProperty) {
                if ($propertyName === $metadata->id) {
                    continue;
                }

                $column = $metadata->columns[$propertyName];

                if ($this->rawData[$hash][$column] !== $newValue = $reflectionProperty->getValue($entity)) {
                    $changeSet[$column] = $newValue;
                }
            }

            if ($changeSet) {
                $this->entityUpdates[$hash] = $entity;
                $this->entityChangeSets[$hash] = $changeSet;
            }
        }
    }

    private function executeInserts(): void
    {
        foreach ($this->entityInsertions as $hash => $entity) {
            $metadata = $this->classMetadataFactory->getMetadata($entity::class);

            $values = [];
            foreach ($metadata->properties as $propertyName => $reflectionProperty) {
                if ($propertyName === $metadata->id) {
                    continue;
                }

                $column = $metadata->columns[$propertyName];
                $values[$column] = $reflectionProperty->getValue($entity);
            }

            $newId = $this->queryBuilder->insert($metadata->table, $values);

            $reflectionProperty = $metadata->idReflection();
            settype($newId, $reflectionProperty->getType()->getName());
            $reflectionProperty->setValue($entity, $newId);

            $this->entities[$hash] = $entity;
            $this->entityIds[$entity::class][$newId] = $hash;
            $this->rawData[$hash] = [$metadata->idColumn() => $newId] + $values;
            $this->entityStates[$hash] = self::STATE_MANAGED;
            unset($this->entityInsertions[$hash]);
        }
    }

    private function executeUpdates(): void
    {
        foreach ($this->entityUpdates as $hash => $entity) {
            $metadata = $this->classMetadataFactory->getMetadata($entity::class);

            $changeSet = $this->entityChangeSets[$hash];

            $this->queryBuilder->update($metadata->table, $changeSet, [
                $metadata->idColumn() => $metadata->idReflection()->getValue($entity),
            ]);

            $this->rawData[$hash] = array_merge($this->rawData[$hash], $changeSet);
            unset(
                $this->entityUpdates[$hash],
                $this->entityChangeSets[$hash],
            );
        }
    }

    private function executeDeletes(): void
    {
        foreach ($this->entityDeletions as $hash => $entity) {
            $metadata = $this->classMetadataFactory->getMetadata($entity::class);

            $this->queryBuilder->delete($metadata->table, [
                $metadata->idColumn() => $id = $metadata->idReflection()->getValue($entity),
            ]);

            unset(
                $this->entities[$hash],
                $this->entityIds[$entity::class][$id],
                $this->rawData[$hash],
                $this->entityStates[$hash],
                $this->entityDeletions[$hash],
            );
        }
    }
}
