<?php

namespace App\Database;
use PDO;

class Connection {

    private PDO $pdo;

    public function __construct($dsn) {
        $this->pdo = new PDO($dsn);
    }
    
}