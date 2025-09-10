<?php

namespace App\Config;

class Database 
{
    private $host = "localhost";
    private $user = "root";
    private $db = "studentdb";
    private $pwd = "";
    private $conn = NULL;
    private $transactionActive = false;

    public function connect() 
    {
        try {
            if ($this->conn) {
                return $this->conn;
            }
            $this->conn = new \PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->pwd);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $exp) {
            echo "Connection Error: " . $exp->getMessage();
        }

        return $this->conn;
    }

    public function beginTransaction()
    {
        if ($this->conn && !$this->transactionActive) {
            $this->transactionActive = $this->conn->beginTransaction();
            return $this->transactionActive;
        }
        return false;
    }

    public function commit()
    {
        if ($this->conn && $this->transactionActive) {
            $result = $this->conn->commit();
            $this->transactionActive = false;
            return $result;
        }
        return false;
    }

    public function rollback()
    {
        if ($this->conn && $this->transactionActive) {
            $result = $this->conn->rollback();
            $this->transactionActive = false;
            return $result;
        }
        return false;
    }

    public function isTransactionActive()
    {
        return $this->transactionActive;
    }

    public function getConnection()
    {
        if ($this->conn) {
            return $this->conn;
        }
        return $this->connect();
    }
}
