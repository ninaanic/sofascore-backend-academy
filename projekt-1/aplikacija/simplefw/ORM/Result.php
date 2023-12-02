<?php

declare(strict_types=1);

namespace SimpleFW\ORM;

final class Result
{
    public function __construct(
        private readonly \PDOStatement $statement,
    ) {
    }

    public function fetchAll(): array
    {
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function fetchOne(): ?array
    {
        return $this->statement->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function count(): int
    {
        return $this->statement->rowCount();
    }
}
