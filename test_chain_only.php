<?php

require_once 'bootstrap.php';

use App\Utils\ChainFunctionality\{LayerRegistration, Executor};
use App\Layers\{SMSAddToQueue, AddStudentEntity, AddLog, ReserveCapacity};
use App\Models\Student;

echo "=== Testing Chain of Responsibility Pattern ===\n\n";

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
$result = $executor->runWithTransaction();

if ($result) {
    echo "All operations completed successfully!\n";
} else {
    echo "Operation failed, rollback executed!\n";
}

echo "\n=== Chain Test Completed ===\n";
