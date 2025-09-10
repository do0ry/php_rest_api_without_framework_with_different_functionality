<?php

namespace App\Config;

use PDO;

class TransactionManager
{
    private PDO $connection;
    private bool $inTransaction = false;
    private static ?TransactionManager $instance = null;

    private function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public static function getInstance(PDO $connection): TransactionManager
    {
        if (self::$instance === null) {
            self::$instance = new self($connection);
        }
        return self::$instance;
    }

    public function beginTransaction(): bool
    {
        if ($this->inTransaction) {
            return true; // Already in transaction
        }

        $result = $this->connection->beginTransaction();
        if ($result) {
            $this->inTransaction = true;
        }
        return $result;
    }

    public function commit(): bool
    {
        if (!$this->inTransaction) {
            return false; // No active transaction
        }

        $result = $this->connection->commit();
        if ($result) {
            $this->inTransaction = false;
        }
        return $result;
    }

    public function rollback(): bool
    {
        if (!$this->inTransaction) {
            return false; // No active transaction
        }

        $result = $this->connection->rollBack();
        if ($result) {
            $this->inTransaction = false;
        }
        return $result;
    }

    public function isInTransaction(): bool
    {
        return $this->inTransaction;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function reset(): void
    {
        $this->inTransaction = false;
    }
}
