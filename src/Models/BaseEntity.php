<?php

namespace App\Models;

use App\Config\TransactionManager;
use App\Config\Database;
use PDO;
use PDOException;

abstract class BaseEntity
{
    protected PDO $connection;
    protected TransactionManager $transactionManager;
    protected string $tableName;
    protected array $fillable = [];
    protected array $guarded = ['id'];
    protected array $attributes = [];

    public function __construct(?PDO $connection = null)
    {
    }

    public static function getPdo(): PDO {
        static $connection;

        if ($connection) {
            return $connection;
        }
        
        return $connection = (new Database())->connect();
    }


    public static function getConnector() {
        return new Database();
    }

    /**
     * Set attribute value
     */
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get attribute value
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Magic method to set attributes
     */
    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Magic method to get attributes
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Check if attribute exists
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Unset attribute
     */
    public function __unset(string $key): void
    {
        unset($this->attributes[$key]);
    }

    /**
     * Get all attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set multiple attributes
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable) && !in_array($key, $this->guarded)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    /**
     * Get fillable attributes
     */
    public function getFillableAttributes(): array
    {
        $fillable = [];
        foreach ($this->fillable as $field) {
            if (!in_array($field, $this->guarded) && isset($this->attributes[$field])) {
                $fillable[$field] = $this->attributes[$field];
            }
        }
        return $fillable;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        if (!$this->transactionManager) {
            $this->transactionManager = TransactionManager::getInstance(static::getConnector());
        }

        return $this->transactionManager->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->transactionManager->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->transactionManager->rollback();
    }

    /**
     * Check if in transaction
     */
    public function isInTransaction(): bool
    {
        return $this->transactionManager->isInTransaction();
    }

    /**
     * Execute query with transaction support
     */
    protected function executeQuery(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if ($this->isInTransaction()) {
                $this->rollback();
            }
            throw $e;
        }
    }

    /**
     * Find record by ID
     */
    public function find(int $id): ?self
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->executeQuery($sql, ['id' => $id]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->fill($row);
            return $this;
        }
        
        return null;
    }

    /**
     * Find all records
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->tableName}";
        $stmt = $this->executeQuery($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Save record (insert or update)
     */
    public function save(): bool
    {
        if (isset($this->attributes['id'])) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    /**
     * Insert new record
     */
    protected function insert(): bool
    {
        $fillable = $this->getFillableAttributes();
        $fields = array_keys($fillable);
        $placeholders = ':' . implode(', :', $fields);
        
        $sql = "INSERT INTO {$this->tableName} (" . implode(', ', $fields) . ") VALUES ({$placeholders})";
        
        $stmt = $this->executeQuery($sql, $fillable);
        
        if ($stmt) {
            $this->setAttribute('id', $this->connection->lastInsertId());
            return true;
        }
        
        return false;
    }

    /**
     * Update existing record
     */
    protected function update(): bool
    {
        if (!isset($this->attributes['id'])) {
            return false;
        }

        $fillable = $this->getFillableAttributes();
        $fields = array_keys($fillable);
        $setClause = implode(' = :', $fields) . ' = :' . implode(', ', $fields);
        
        $sql = "UPDATE {$this->tableName} SET {$setClause} WHERE id = :id";
        
        $params = array_merge($fillable, ['id' => $this->attributes['id']]);
        
        $stmt = $this->executeQuery($sql, $params);
        return $stmt !== false;
    }

    /**
     * Delete record
     */
    public function delete(): bool
    {
        if (!isset($this->attributes['id'])) {
            return false;
        }

        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->executeQuery($sql, ['id' => $this->attributes['id']]);
        
        if ($stmt) {
            unset($this->attributes['id']);
            return true;
        }
        
        return false;
    }

    /**
     * Find records by condition
     */
    public function where(string $field, $operator, $value): array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE {$field} {$operator} :value";
        $stmt = $this->executeQuery($sql, ['value' => $value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get table name
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Convert to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->attributes);
    }
}
