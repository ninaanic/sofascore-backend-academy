<?php

declare(strict_types=1);

namespace SimpleFW\ORM;

use SimpleFW\ORM\Exception\ConnectionFailedException;
use SimpleFW\ORM\Exception\LastInsertIdNotDefinedException;
use SimpleFW\ORM\Exception\QueryFailedException;

final class Connection
{
    private \PDO $pdo;

    public function __construct(
        private readonly string $dsn,
    ) {
    }

    public function execute(string $query, array $params = []): int
    {
        $statement = $this->prepare($query, $params);

        try {
            $statement->execute();
        } catch (\PDOException $e) {
            throw new QueryFailedException($e);
        }

        return $statement->rowCount();
    }

    public function query(string $query, array $params = []): Result
    {
        $statement = $this->prepare($query, $params);

        try {
            $statement->execute();
        } catch (\PDOException $e) {
            throw new QueryFailedException($e);
        }

        return new Result($statement);
    }

    public function lastInsertId(): string
    {
        try {
            return $this->connection()->lastInsertId();
        } catch (\PDOException $e) {
            throw new LastInsertIdNotDefinedException($e);
        }
    }

    private function connection(): \PDO
    {
        try {
            return $this->pdo ??= new \PDO($this->dsn);
        } catch (\PDOException $e) {
            throw new ConnectionFailedException($e);
        }
    }

    private function prepare(string $query, array $params = []): \PDOStatement
    {
        $statement = $this->connection()->prepare($query);

        foreach ($params as $paramName => $paramValue) {
            $type = match (true) {
                \is_int($paramValue) => \PDO::PARAM_INT,
                \is_bool($paramValue) => \PDO::PARAM_BOOL,
                null === $paramValue => \PDO::PARAM_NULL,
                default => \PDO::PARAM_STR,
            };

            $statement->bindValue($paramName, $paramValue, $type);
        }

        return $statement;
    }
}
