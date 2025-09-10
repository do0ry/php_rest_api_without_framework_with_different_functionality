<?php

require_once '../../bootstrap.php';

use App\Utils\ChainFunctionality\{LayerRegistration, Executor};
use App\Layers\{SMSAddToQueue, AddStudentEntity, AddLog, ReserveCapacity};

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

echo "=== Chain of Responsibility Test ===\n";

try {
    // Create layer instances
    $smsLayer = new SMSAddToQueue();
    $entityLayer = new AddStudentEntity();
    $logLayer = new AddLog();
    $capacityLayer = new ReserveCapacity();

    // Chain the layers together
    $smsLayer->linkWith($logLayer)
             ->linkWith($capacityLayer)
             ->linkWith($entityLayer);

    // Create executor and set the logic
    $executor = new Executor();
    $executor->setLogic($smsLayer);

    echo "Running chain without transaction...\n";
    $result = $executor->runWithoutTransaction();

    if ($result) {
        echo "All operations completed successfully!\n";
        echo json_encode(['status' => 'success', 'message' => 'Chain executed successfully']);
    } else {
        echo "Operation failed, rollback executed!\n";
        echo json_encode(['status' => 'error', 'message' => 'Chain execution failed']);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

echo "\n=== Chain Test Completed ===\n";
