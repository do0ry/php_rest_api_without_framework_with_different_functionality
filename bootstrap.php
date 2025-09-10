<?php

require_once 'vendor/autoload.php';

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Only set CORS headers if running in web context
if (php_sapi_name() !== 'cli') {
    // Set up CORS headers
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    // Handle preflight OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}
