<?php

namespace App\Models;

class Student extends BaseEntity
{
    protected string $tableName = 'students';
    protected array $fillable = ['name', 'address', 'age'];
    protected array $guarded = ['id'];

    public function __construct($db = null)
    {
        parent::__construct($db);
    }

    /**
     * Legacy method for backward compatibility
     */
    public function fetchAll()
    {
        return $this->findAll();
    }

    /**
     * Legacy method for backward compatibility
     */
    public function fetchOne()
    {
        if (isset($this->attributes['id'])) {
            $result = $this->find($this->attributes['id']);
            return $result !== null;
        }
        return false;
    }

    /**
     * Legacy method for backward compatibility
     */
    public function postData()
    {
        return $this->save();
    }

    /**
     * Legacy method for backward compatibility
     */
    public function putData()
    {
        return $this->save();
    }

    /**
     * Get students by age range
     */
    public function getByAgeRange(int $minAge, int $maxAge): array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE age BETWEEN :min_age AND :max_age";
        $stmt = $this->executeQuery($sql, [
            'min_age' => $minAge,
            'max_age' => $maxAge
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get students by name (partial match)
     */
    public function getByName(string $name): array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE name LIKE :name";
        $stmt = $this->executeQuery($sql, ['name' => "%{$name}%"]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get students by address
     */
    public function getByAddress(string $address): array
    {
        return $this->where('address', 'LIKE', "%{$address}%");
    }

    /**
     * Get student statistics
     */
    public function getStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_students,
                    AVG(age) as average_age,
                    MIN(age) as min_age,
                    MAX(age) as max_age
                FROM {$this->tableName}";
        
        $stmt = $this->executeQuery($sql);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Bulk insert students
     */
    public function bulkInsert(array $students): bool
    {
        if (empty($students)) {
            return false;
        }

        $this->beginTransaction();
        
        try {
            foreach ($students as $studentData) {
                $this->fill($studentData);
                if (!$this->save()) {
                    throw new \Exception("Failed to insert student: " . json_encode($studentData));
                }
            }
            
            return $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Update multiple students
     */
    public function bulkUpdate(array $updates): bool
    {
        if (empty($updates)) {
            return false;
        }

        $this->beginTransaction();
        
        try {
            foreach ($updates as $update) {
                if (!isset($update['id'])) {
                    throw new \Exception("ID is required for update");
                }
                
                $this->fill($update);
                if (!$this->save()) {
                    throw new \Exception("Failed to update student ID: " . $update['id']);
                }
            }
            
            return $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Delete multiple students
     */
    public function bulkDelete(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "DELETE FROM {$this->tableName} WHERE id IN ({$placeholders})";
        
        $this->beginTransaction();
        
        try {
            $stmt = $this->executeQuery($sql, $ids);
            return $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
