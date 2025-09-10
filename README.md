# PHP REST API with Advanced Patterns

A modern PHP REST API built without frameworks, featuring advanced architectural patterns including Chain of Responsibility, PSR-4 autoloading, and database transaction management.

## Features

- **RESTful API** - Clean REST endpoints for student management
- **Chain of Responsibility Pattern** - Modular request processing pipeline
- **PSR-4 Autoloading** - Modern PHP namespace structure with Composer
- **Database Transactions** - ACID-compliant database operations
- **Docker Support** - Easy MySQL setup with Docker Compose
- **Entity Framework** - Abstract BaseEntity class for data models
- **Transaction Management** - Singleton pattern for database transactions

## Project Structure

```
├── src/
│   ├── Api/                 # REST API controllers
│   ├── Config/              # Database and transaction configuration
│   ├── Models/              # Data models and entities
│   ├── Utils/
│   │   └── ChainFunctionality/  # Chain of Responsibility pattern
│   └── Layers/              # Chain processing layers
├── public/                  # Web-accessible files
├── db/                      # Database initialization scripts
├── vendor/                  # Composer dependencies
└── docker-compose.yml       # MySQL container configuration
```

## Installation

### Prerequisites
- PHP 8.1+
- Composer
- Docker & Docker Compose

### Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd php_rest_api_without_framework_with_different_functionality
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Start MySQL with Docker**
   ```bash
   ./start_mysql.sh
   ```

4. **Start the PHP server**
   ```bash
   ./start_server.sh
   ```

## API Endpoints

### Students Management
- `GET /api/students.php` - Get all students
- `POST /api/students.php` - Create a new student
- `PUT /api/students.php` - Update a student
- `DELETE /api/students.php` - Delete a student

### Chain of Responsibility Demo
- `POST /api/students_chain_simple.php` - Process student with chain pattern

## Chain of Responsibility Pattern

The project implements a sophisticated Chain of Responsibility pattern for request processing:

```php
// Chain layers
$smsLayer = new SMSAddToQueue();
$logLayer = new AddLog();
$capacityLayer = new ReserveCapacity();
$entityLayer = new AddStudentEntity();

// Link the chain
$smsLayer->linkWith($logLayer)
         ->linkWith($capacityLayer)
         ->linkWith($entityLayer);

// Execute with transaction support
$executor = new Executor();
$executor->setLogic($smsLayer);
$result = $executor->runWithTransaction($transactionManager);
```

## Database

The project uses MySQL with Docker for easy setup:

- **Database**: `studentdb`
- **Table**: `students` (id, name, address, age)
- **Connection**: PDO with transaction support
- **Port**: 3306

## Architecture Patterns

### 1. Chain of Responsibility
- Modular request processing
- Easy to add/remove processing steps
- Transaction-aware execution

### 2. Singleton Pattern
- TransactionManager for database transactions
- Ensures single transaction context

### 3. Repository Pattern
- BaseEntity abstract class
- Consistent data access methods
- Transaction integration

### 4. PSR-4 Autoloading
- Modern PHP namespace structure
- Composer-based dependency management
- Clean code organization

## Testing

Test the Chain of Responsibility pattern:

```bash
# CLI test
php test_chain_only.php

# API test
curl -X POST http://localhost:8000/api/students_chain_simple.php \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Student","address":"123 Test St","age":25}'
```

## Environment Configuration

Create a `.env` file:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=studentdb
DB_USER=root
DB_PASSWORD=password
```

## Usage Examples

### Create a Student
```bash
curl -X POST http://localhost:8000/api/students.php \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","address":"123 Main St","age":25}'
```

### Get All Students
```bash
curl http://localhost:8000/api/students.php
```

## Development

The project is designed for learning and demonstrates:
- Modern PHP practices
- Design patterns implementation
- Database transaction management
- RESTful API design
- Docker containerization

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

**Neda** - *Initial work*

---

Built with pure PHP and modern architectural patterns.
