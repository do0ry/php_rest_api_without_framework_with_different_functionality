<?php

require_once '../../bootstrap.php';

use App\Config\Database;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

echo "Testing database connection...\n";

$db = new Database();
$connection = $db->connect();

if ($connection) {
    echo "✅ Database connection successful!\n";
    
    $stmt = $connection->query('SELECT COUNT(*) as total FROM students');
    $result = $stmt->fetch();
    echo "Total students: " . $result['total'] . "\n";
} else {
    echo "❌ Database connection failed!\n";
}
