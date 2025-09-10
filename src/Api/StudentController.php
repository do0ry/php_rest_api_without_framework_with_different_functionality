<?php

namespace App\Api;

use App\Config\Database;
use App\Models\Student;

class StudentController
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function getAll()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $connection = $this->database->connect();
                
                if (!$connection) {
                    throw new \Exception("Database connection failed");
                }

                $student = new Student($connection);
                $students = $student->findAll();

                if (!empty($students)) {
                    echo json_encode($students);
                } else {
                    echo json_encode(array('message' => "No records found!"));
                }
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(array('message' => "Error: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array('message' => "Error: incorrect Method!"));
        }
    }

    public function getOne($id)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $connection = $this->database->connect();
                
                if (!$connection) {
                    throw new \Exception("Database connection failed");
                }

                $student = new Student($connection);
                $result = $student->find($id);

                if ($result) {
                    echo json_encode($result->toArray());
                } else {
                    echo json_encode(array('message' => "No records found!"));
                }
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(array('message' => "Error: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array('message' => "Error: incorrect Method!"));
        }
    }

    public function create()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = json_decode(file_get_contents("php://input"), true);

                if (!$data) {
                    throw new \Exception("Invalid JSON data");
                }

                $connection = $this->database->connect();
                
                if (!$connection) {
                    throw new \Exception("Database connection failed");
                }

                $student = new Student($connection);

                // Begin transaction
                $student->beginTransaction();

                try {
                    $student->fill($data);
                    
                    if ($student->save()) {
                        $student->commit();
                        echo json_encode(array(
                            'message' => "Student created successfully!",
                            'data' => $student->toArray()
                        ));
                    } else {
                        $student->rollback();
                        echo json_encode(array('message' => "Student could not be created!"));
                    }
                } catch (\Exception $e) {
                    $student->rollback();
                    throw $e;
                }
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(array('message' => "Error: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array('message' => "Error: incorrect Method!"));
        }
    }

    public function getStatistics()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $connection = $this->database->connect();
                
                if (!$connection) {
                    throw new \Exception("Database connection failed");
                }

                $student = new Student($connection);
                $stats = $student->getStatistics();
                echo json_encode($stats);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(array('message' => "Error: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array('message' => "Error: incorrect Method!"));
        }
    }
}
