<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private $host = 'db'; // ou 'localhost' se estiver fora do Docker
    private $db_name = 'scheduler';
    private $username = 'adminsch';
    private $password = 'admin123';
    private $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro na conexão com o banco: " . $e->getMessage());
        }

        return $this->conn;
    }
}
