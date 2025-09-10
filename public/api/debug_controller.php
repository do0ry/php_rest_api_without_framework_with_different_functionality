<?php

require_once '../../bootstrap.php';

use App\Config\Database;
use App\Models\Student;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


$db = new Database();

$connection = $db->connect();

if ($connection) {
    
    $student = new Student($connection);
    
    $students = $student->findAll();
    
    echo json_encode($students);
} else {
}
