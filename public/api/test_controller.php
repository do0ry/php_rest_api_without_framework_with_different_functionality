<?php

require_once '../../bootstrap.php';

use App\Api\StudentController;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

echo "Testing StudentController...\n";

try {
    $controller = new StudentController();
    echo "✅ StudentController created successfully!\n";
    
    $controller->getAll();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
