<?php

declare(strict_types=1);

namespace SimpleFW\ORM;

final class QueryBuilder
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function startTransaction(): void
    {
        $this->connection->execute('START TRANSACTION');
    }

    public function commit(): void
    {
        $this->connection->execute('COMMIT');
    }

    public function rollback(): void
    {
        $this->connection->execute('ROLLBACK');
    }

    public function insert(string $table, array $params): string
    {
        $fields = array_keys($params);

        $query = sprintf(
            'INSERT INTO %s(%s) VALUES(%s)',
            $table,
            implode(', ', $fields),
            implode(', ', array_map(static fn (string $field) => ':'.$field, $fields)),
        );

        $this->connection->execute($query, $params);

        return $this->connection->lastInsertId();
    }

    public function update(string $table, array $params, array $id): int
    {
        $query = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', array_map(static fn (string $field) => "$field = :$field", array_keys($params))),
            implode(' AND ', array_map(static fn (string $field) => "$field = :$field", array_keys($id))),
        );

        return $this->connection->execute($query, $params + $id);
    }

    public function delete(string $table, array $id): int
    {
        $query = sprintf(
            'DELETE FROM %s WHERE %s',
            $table,
            implode(' AND ', array_map(static fn (string $field) => "$field = :$field", array_keys($id))),
        );

        return $this->connection->execute($query, $id);
    }

    public function find(string $table, array $columns = [], array $criteria = [], ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->select($table, $columns, $criteria, $orderBy, $limit, $offset)->fetchAll();
    }

    public function findOne(string $table, array $columns = [], array $criteria = [], ?array $orderBy = null): ?array
    {
        return $this->select($table, $columns, $criteria, $orderBy, 1)->fetchOne();
    }

    private function select(string $table, array $columns = [], array $criteria = [], ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Result
    {
        $query = sprintf(
            'SELECT %s FROM %s',
            $columns ? implode(', ', $columns) : '*',
            $table,
        );

        if ($criteria) {
            $query .= sprintf(
                ' WHERE %s',
                implode(' AND ', array_map(static fn (string $field) => "$field = :$field", array_keys($criteria))),
            );
        }

        if ($orderBy) {
            $query .= sprintf(
                ' ORDER BY %s',
                implode(', ', array_map(static fn (string $field, string $direction) => "$field $direction", array_keys($orderBy), $orderBy)),
            );
        }

        if (null !== $limit) {
            $query .= sprintf(' LIMIT %d', $limit);
        }

        if (null !== $offset) {
            $query .= sprintf(' OFFSET %d', $offset);
        }

        return $this->connection->query($query, $criteria);
    }
}
