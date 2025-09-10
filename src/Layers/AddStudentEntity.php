<?php

namespace App\Layers;

use App\Utils\ChainFunctionality\LayerRegistration;
use App\Models\Student;

class AddStudentEntity extends LayerRegistration
{
    private $studentData;
    private $database;

    public function setStudentData($data)
    {
        $this->studentData = $data;
    }

    public function setDatabase($database)
    {
        $this->database = $database;
    }

    public function execute()
    {
        print "IN EXECUTION add entity\n";
        
        if ($this->studentData && $this->database) {
            // Create student instance and add to database
            $student = new Student($this->database);
            $student->fill($this->studentData);
            
            if (!$student->save()) {
                throw new \Exception('Failed to add student entity to database');
            }
            
            echo "Student entity added successfully with ID: " . $student->getAttribute('id') . "\n";
        } else {
            throw new \Exception('Student data or database connection not available');
        }
        
        // Comment out the next line to test successful execution
        // throw new \Exception('Simulated error for testing rollback');
        
        return parent::execute();
    }

    public function rollback()
    {
        print "IN ROLLBACK undo add entity\n";
        
        if ($this->studentData && $this->database) {
            // In a real scenario, you would delete the added student
            // For now, we'll just log the rollback action
            echo "Rolling back student entity addition\n";
            
            // If we have the student ID, we could delete it here
            if (isset($this->studentData['id'])) {
                $student = new Student($this->database);
                $student->setAttribute('id', $this->studentData['id']);
                $student->delete();
                echo "Student with ID " . $this->studentData['id'] . " deleted during rollback\n";
            }
        }
        
        return parent::rollback();
    }
}
