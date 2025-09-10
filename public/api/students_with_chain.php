<?php

require_once '../../bootstrap.php';

use App\Utils\ChainFunctionality\{LayerRegistration, Executor};
use App\Layers\{SMSAddToQueue, AddStudentEntity, AddLog, ReserveCapacity};
use App\Config\Database;
use App\Config\TransactionManager;
use App\Models\Student;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$data) {
            throw new \Exception("Invalid JSON data");
        }

        // Create database connection
        $db = new Database();
        $connection = $db->connect();
        
        if (!$connection) {
            throw new \Exception("Database connection failed");
        }

        // Create transaction manager
        $transactionManager = TransactionManager::getInstance($connection);

        // Create layer instances
        $smsLayer = new SMSAddToQueue();
        $entityLayer = new AddStudentEntity();
        $logLayer = new AddLog();
        $capacityLayer = new ReserveCapacity();

        // Set student data for the entity layer
        $entityLayer->setStudentData($data);

        // Chain the layers together
        $smsLayer->linkWith($logLayer)
                 ->linkWith($capacityLayer)
                 ->linkWith($entityLayer);

        // Create executor and set the logic
        $executor = new Executor();
        $executor->setLogic($smsLayer);
        $executor->setTransactionManager($transactionManager);

        echo "Running chain with transaction for student creation...\n";
        $result = $executor->runLogic();

        if ($result) {
            echo "Student created successfully through chain pattern!\n";
            echo json_encode([
                'status' => 'success', 
                'message' => 'Student created successfully through chain pattern!',
                'data' => $data
            ]);
        } else {
            echo "Student creation failed, rollback executed!\n";
            echo json_encode([
                'status' => 'error', 
                'message' => 'Student creation failed, rollback executed!'
            ]);
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Only POST method allowed for chain operations'
    ]);
}
