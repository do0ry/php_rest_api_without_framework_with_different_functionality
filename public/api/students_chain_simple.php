<?php

require_once '../../bootstrap.php';

use App\Utils\ChainFunctionality\{LayerRegistration, Executor};
use App\Layers\{SMSAddToQueue, AddStudentEntity, AddLog, ReserveCapacity};

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

        echo "=== Student Creation with Chain of Responsibility ===\n";
        echo "Student Data: " . json_encode($data) . "\n\n";

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

        echo "Running chain without transaction...\n";
        $result = $executor->runWithTransaction();

        if ($result) {
            echo "Student processing completed successfully through chain pattern!\n";
            echo json_encode([
                'status' => 'success', 
                'message' => 'Student processing completed successfully through chain pattern!',
                'data' => $data,
                'chain_execution' => 'completed'
            ]);
        } else {
            echo "Student processing failed, rollback executed!\n";
            echo json_encode([
                'status' => 'error', 
                'message' => 'Student processing failed, rollback executed!'
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
