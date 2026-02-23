# Campus Assignment Management API

A RESTful API for managing college assignments built with Laravel 11 

## 📋 Features

- **Authentication Module**
  - Student Registration
  - JWT-based Login/Logout
  - Profile Management
  - Secure password hashing

- **Assignment Management (CRUD)**
  - Create Assignments
  - View All User Assignments
  - Update Assignment Details
  - Delete Assignments
  - Status Tracking (Pending/Submitted/Approved)

##  Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd assignment-api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   
   Edit your `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=assignment_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

## 📡 API Endpoints

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/auth/register` | Register new student | No |
| POST | `/api/auth/login` | User login | No |
| POST | `/api/auth/logout` | User logout | Yes |
| GET | `/api/auth/profile` | Get user profile | Yes |

### Assignments

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/assignments` | Get all user assignments | Yes |
| POST | `/api/assignments` | Create new assignment | Yes |
| GET | `/api/assignments/{id}` | Get specific assignment | Yes |
| PUT | `/api/assignments/{id}` | Update assignment | Yes |
| DELETE | `/api/assignments/{id}` | Delete assignment | Yes |

## 📝 Sample Requests & Responses

### Register User

**Request:**
```bash
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "department": "Computer Science",
    "year": 3
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Login

**Request:**
```bash
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Get Profile

**Request:**
```bash
GET /api/auth/profile
Authorization: Bearer 1|abc123...
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "department": "Computer Science",
        "year": 3
    }
}
```

### Create Assignment

**Request:**
```bash
POST /api/assignments
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
    "title": "Database Design Project",
    "description": "Create a comprehensive database schema for a library management system",
    "subject": "Database Systems",
    "status": "Pending"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Assignment created successfully",
    "data": {
        "id": 1,
        "title": "Database Design Project",
        "description": "Create a comprehensive database schema for a library management system",
        "subject": "Database Systems",
        "status": "Pending",
        "user_id": 1
    }
}
```

### Get Specific Assignment

**Request:**
```bash
GET /api/assignments/1
Authorization: Bearer 1|abc123...
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Database Design Project",
        "description": "Create a comprehensive database schema for a library management system",
        "subject": "Database Systems",
        "status": "Pending",
        "user_id": 1
    }
}
```

### Update Assignment

**Request:**
```bash
PUT /api/assignments/1
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
    "title": "Updated Database Design Project",
    "status": "Submitted"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Assignment updated successfully",
    "data": {
        "id": 1,
        "title": "Updated Database Design Project",
        "description": "Create a comprehensive database schema for a library management system",
        "subject": "Database Systems",
        "status": "Submitted",
        "user_id": 1
    }
}
```

### Delete Assignment

**Request:**
```bash
DELETE /api/assignments/1
Authorization: Bearer 1|abc123...
```

**Response:**
```json
{
    "success": true,
    "message": "Assignment deleted successfully"
}
```

### Error Responses

#### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    },
    "error_code": "VALIDATION_ERROR"
}
```

#### Authentication Error (401)
```json
{
    "success": false,
    "message": "Invalid credentials",
    "error_code": "INVALID_CREDENTIALS"
}
```

#### Not Found Error (404)
```json
{
    "success": false,
    "message": "Assignment not found or access denied",
    "error_code": "ASSIGNMENT_NOT_FOUND"
}
```

#### Access Denied Error (403)
```json
{
    "success": false,
    "message": "Access denied",
    "error_code": "ACCESS_DENIED"
}
```

#### Rate Limit Error (429)
```json
{
    "success": false,
    "message": "Too many requests. Please try again later.",
    "error_code": "TOO_MANY_REQUESTS"
}
```
