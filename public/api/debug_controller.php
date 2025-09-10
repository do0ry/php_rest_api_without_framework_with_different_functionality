<?php

require_once '../../bootstrap.php';

use App\Config\Database;
use App\Models\Student;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

echo "Debugging step by step...\n";

echo "1. Creating Database instance...\n";
$db = new Database();

echo "2. Connecting to database...\n";
$connection = $db->connect();

if ($connection) {
    echo "✅ Database connection successful!\n";
    
    echo "3. Creating Student instance...\n";
    $student = new Student($connection);
    
    echo "4. Calling findAll()...\n";
    $students = $student->findAll();
    
    echo "5. Found " . count($students) . " students\n";
    echo json_encode($students);
} else {
    echo "❌ Database connection failed!\n";
}
