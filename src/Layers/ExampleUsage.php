<?php

namespace App\Layers;

use App\Utils\ChainFunctionality\{LayerRegistration, Executor};
use App\Layers\{SMSAddToQueue, AddStudentEntity, AddLog, ReserveCapacity};

class ExampleUsage
{
    public static function runExample()
    {
        // Example usage of the chain of responsibility pattern
        $smsLayer = new SMSAddToQueue();
        $entityLayer = new AddStudentEntity();
        $logLayer = new AddLog();
        $capacityLayer = new ReserveCapacity();

        // Chain the layers together
        $smsLayer->linkWith($entityLayer)
                 ->linkWith($logLayer)
                 ->linkWith($capacityLayer);

        // Create executor and set the logic
        $executor = new Executor();
        $executor->setLogic($smsLayer);

        // Run the logic
        echo "Running the chain of responsibility...\n";
        $result = $executor->runLogic();

        if ($result) {
            echo "All operations completed successfully!\n";
        } else {
            echo "Operation failed, rollback executed!\n";
        }
    }
}
