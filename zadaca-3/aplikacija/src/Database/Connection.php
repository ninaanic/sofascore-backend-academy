<?php

namespace App\Database;
use PDO;

class Connection {

    private PDO $pdo;

    public function __construct($dsn) {
        $this->pdo = new PDO($dsn);
    }

    public function query($query, $params = array()) {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);

        if (explode(' ', $query)[0] == 'SELECT') {
            $data = $statement->fetchAll();
            return $data;
        }
    }

    public function insert(string $table, array $params): int
    {
        $fields = array_keys($params);

        $sql = sprintf(
            'INSERT INTO %s(%s) VALUES(%s)',
            $table,
            implode(', ', $fields),
            implode(', ', array_map(static fn (string $field) => ':'.$field, $fields)),
        );

        $statement = $this->pdo->prepare($sql);

        $statement->execute($params);

        return $this->pdo->lastInsertId();
    }

    public function update(string $table, array $params, int $id): int
    {
        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $table,
            implode(', ', array_map(static fn (string $field) => "$field = :$field", array_keys($params))),
        );

        $statement = $this->pdo->prepare($sql);

        $statement->execute($params + ['id' => $id]);

        return $statement->rowCount();
    }

    public function find(string $table, array $fields = [], array $where = []): array
    {
        return $this->select($table, $fields, $where)->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function findOne(string $table, array $fields = [], array $where = []): ?array
    {
        return $this->select($table, $fields, $where, 1)->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function select(string $table, array $fields = [], array $where = [], ?int $limit = null): \PDOStatement
    {
        $sql = sprintf(
            'SELECT %s FROM %s',
            $fields ? implode(', ', $fields) : '*',
            $table,
        );

        if ($where) {
            $sql .= sprintf(
                ' WHERE %s',
                implode(' AND ', array_map(static fn (string $field) => "$field = :$field", array_keys($where))),
            );
        }

        if ($limit) {
            $sql .= sprintf(' LIMIT %d', $limit);
        }

        $statement = $this->pdo->prepare($sql);

        $statement->execute($where);

        return $statement;
    }
    
}