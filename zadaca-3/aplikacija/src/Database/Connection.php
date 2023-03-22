<?php

namespace App\Database;
use PDO;

class Connection {

    private PDO $pdo;

    public function __construct($dsn) {
        $this->pdo = $dsn;
    }

    public function query($query, $params = array()) {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);

        if (explode(' ', $query)[0] == 'SELECT') {
            $data = $statement->fetchAll();
            return $data;
        }
    }
    
}