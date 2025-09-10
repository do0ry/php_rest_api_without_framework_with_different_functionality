#!/bin/bash

echo "Available endpoints:"
echo "  • Web Interface: http://localhost:8000"
echo "  • API Endpoint:  http://localhost:8000/api/students.php"
echo ""
echo "API Methods:"
echo "  • GET    /api/students.php           - Get all students"
echo "  • GET    /api/students.php?id=1      - Get student by ID"
echo "  • POST   /api/students.php           - Create student"
echo "  • PUT    /api/students.php           - Update student"
echo "  • DELETE /api/students.php           - Delete student"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Start PHP built-in server
php -S localhost:8000 -t public/
