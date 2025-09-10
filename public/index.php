<?php

require_once '../bootstrap.php';

use App\Layers\ExampleUsage;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP REST API - PSR-4 Structure</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .endpoint { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .method { font-weight: bold; color: #007bff; }
        .url { font-family: monospace; background: #e9ecef; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>PHP REST API with PSR-4 Namespaces</h1>
        
        <h2>Available API Endpoints</h2>
        
        <div class="endpoint">
            <span class="method">GET</span> <span class="url">/api/students.php</span>
            <p>Get all students</p>
        </div>
        
        <div class="endpoint">
            <span class="method">GET</span> <span class="url">/api/students.php?id=1</span>
            <p>Get student by ID</p>
        </div>
        
        <div class="endpoint">
            <span class="method">POST</span> <span class="url">/api/students.php</span>
            <p>Create new student</p>
        </div>
        
        <div class="endpoint">
            <span class="method">PUT</span> <span class="url">/api/students.php</span>
            <p>Update student</p>
        </div>
        
        <div class="endpoint">
            <span class="method">DELETE</span> <span class="url">/api/students.php</span>
            <p>Delete student</p>
        </div>
        
        <h2>Chain of Responsibility Pattern Test</h2>
        <p>Click the button below to test the chain of responsibility pattern:</p>
        <button onclick="testChain()">Test Chain Pattern</button>
        <div id="result"></div>
        
        <script>
            function testChain() {
                fetch('/api/students.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: 'Test Student',
                        address: 'Test Address',
                        age: 25
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('result').innerHTML = 
                        '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(error => {
                    document.getElementById('result').innerHTML = 
                        '<p style="color: red;">Error: ' + error.message + '</p>';
                });
            }
        </script>
    </div>
</body>
</html>
