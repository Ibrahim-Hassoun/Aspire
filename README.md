# Inventory Management System

A comprehensive inventory management system for product inventory, user roles, and chatbot integration. Below is a summary of what has been accomplished so far:

## ✅ Accomplished Features & Technical Highlights

### Backend (Laravel)

- Products table migration with all required fields, including `imageurl` for product images
- Product model with search, filtering, and statistics scopes
- Full CRUD API for products (list, create, get, delete, bulk delete)
- Request validation for all product operations
- Role-based access: only admin (id=1) can delete products, enforced via middleware and User model
- Factories and seeders for products and users, with realistic data and real image URLs
- Admin and moderator users seeded for stakeholder testing (admin@email.com / password, moderator@email.com / password)
- Helper command to refresh test data (`php artisan test:refresh --fresh`)
- Comprehensive API documentation and example requests/responses
- Error handling and validation rules documented

### Chatbot Integration

- Gemini/Prism chatbot integration with direct HTTP calls and SSL fixes
- Chatbot endpoints for real-time inventory queries

### Frontend (React)

- Modern React frontend (Vite) ready for API integration

### Testing & Stakeholder Readiness

- All endpoints tested and documented
- Realistic test data for products and users
- Role-based access and constraints enforced

### Database Schema (Products Table)

| Column      | Type          | Description         |
| ----------- | ------------- | ------------------- |
| id          | bigint        | Primary key         |
| name        | varchar(255)  | Product name        |
| quantity    | int           | Stock quantity      |
| category    | varchar(255)  | Product category    |
| description | text          | Product description |
| price       | decimal(10,2) | Product price       |
| status      | enum          | Product status      |
| imageurl    | varchar(255)  | Product image URL   |
| created_at  | timestamp     | Creation time       |
| updated_at  | timestamp     | Last update time    |

---

A comprehensive inventory management system built with Laravel (backend) and React (frontend), featuring authentication, product management, and real-time chatbot integration.

## 🚀 Features

- **Authentication System**: JWT-based authentication with registration, login, logout, and profile management
- **Product Management**: Complete CRUD operations with search, filtering, and bulk operations
- **Inventory Tracking**: Real-time stock monitoring with low-stock alerts
- **Statistics Dashboard**: Comprehensive analytics and reporting
- **Chatbot Integration**: AI-powered chatbot for inventory queries
- **Responsive Design**: Modern React frontend with Vite

## 🛠️ Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: React, Vite
- **Database**: MySQL/PostgreSQL
- **Authentication**: JWT (tymon/jwt-auth)
- **Logging**: Laravel Log system

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL/PostgreSQL database

## 🔧 Installation

### Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

### Frontend Setup

```bash
cd frontend
npm install
npm run dev
```

## 📚 API Documentation

Base URL: `http://localhost:8000/api`

All authenticated endpoints require the `Authorization: Bearer <token>` header.

---

## 🔐 Authentication APIs

### Register User

```http
POST /api/auth/register
```

**Request Body:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**

```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  }
}
```

### Login

```http
POST /api/auth/login
```

**Request Body:**

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

### Logout

```http
POST /api/auth/logout
```

_Requires authentication_

### Refresh Token

```http
POST /api/auth/refresh
```

_Requires authentication_

### Get Profile

```http
GET /api/auth/profile
```

_Requires authentication_

---

## 📦 Products APIs

### List Products (with Search & Filtering)

```http
GET /api/products
```

**Query Parameters:**
| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `search` | string | Search in name/description | `?search=laptop` |
| `category` | string | Filter by category | `?category=electronics` |
| `status` | string | Filter by status | `?status=in_stock` |
| `min_price` | number | Minimum price filter | `?min_price=100` |
| `max_price` | number | Maximum price filter | `?max_price=1000` |
| `sort_by` | string | Sort field | `?sort_by=price` |
| `sort_order` | string | Sort direction (asc/desc) | `?sort_order=asc` |
| `per_page` | number | Items per page (max 100) | `?per_page=20` |
| `page` | number | Page number | `?page=2` |
| `low_stock_threshold` | number | Low stock filter | `?low_stock_threshold=5` |

**Valid status values:** `in_stock`, `low_stock`, `ordered`, `discontinued`  
**Valid sort_by values:** `name`, `price`, `quantity`, `created_at`, `updated_at`

**Example Request:**

```http
GET /api/products?search=mouse&category=electronics&status=in_stock&min_price=10&max_price=100&sort_by=price&sort_order=asc&per_page=15
```

**Response:**

```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": {
    "products": [
      {
        "id": 1,
        "name": "Wireless Mouse",
        "quantity": 50,
        "category": "Electronics",
        "description": "Bluetooth wireless mouse",
        "price": "29.99",
        "status": "in_stock",
        "created_at": "2025-07-29T10:30:00.000000Z",
        "updated_at": "2025-07-29T10:30:00.000000Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 3,
      "per_page": 15,
      "total": 45,
      "from": 1,
      "to": 15
    },
    "filters_applied": {
      "search": "mouse",
      "category": "electronics",
      "status": "in_stock"
    }
  }
}
```

### Create Product

```http
POST /api/products
```

**Request Body:**

```json
{
  "name": "Wireless Keyboard",
  "quantity": 25,
  "category": "Electronics",
  "description": "Mechanical wireless keyboard with RGB lighting",
  "price": 89.99,
  "status": "in_stock"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "product": {
      "id": 2,
      "name": "Wireless Keyboard",
      "quantity": 25,
      "category": "Electronics",
      "description": "Mechanical wireless keyboard with RGB lighting",
      "price": "89.99",
      "status": "in_stock",
      "created_at": "2025-07-29T10:35:00.000000Z",
      "updated_at": "2025-07-29T10:35:00.000000Z"
    }
  }
}
```

### Get Single Product

```http
GET /api/products/{id}
```

**Response:**

```json
{
  "success": true,
  "message": "Product retrieved successfully",
  "data": {
    "product": {
      "id": 1,
      "name": "Wireless Mouse",
      "quantity": 50,
      "category": "Electronics",
      "description": "Bluetooth wireless mouse",
      "price": "29.99",
      "status": "in_stock",
      "created_at": "2025-07-29T10:30:00.000000Z",
      "updated_at": "2025-07-29T10:30:00.000000Z"
    }
  }
}
```

### Delete Single Product

```http
DELETE /api/products/{id}
```

**Response:**

```json
{
  "success": true,
  "message": "Product deleted successfully",
  "data": {
    "deleted_product_id": 1
  }
}
```

### Delete Multiple Products

```http
DELETE /api/products/bulk
```

**Request Body:**

```json
{
  "ids": [1, 2, 3, 5],
  "force": false
}
```

**Response:**

```json
{
  "success": true,
  "message": "Deletion completed: 3 deleted, 1 failed, 0 not found",
  "data": {
    "results": {
      "deleted": [
        { "id": 1, "name": "Product 1" },
        { "id": 2, "name": "Product 2" }
      ],
      "failed": [{ "id": 5, "error": "High-value product constraint" }],
      "not_found": []
    },
    "summary": {
      "deleted_count": 2,
      "failed_count": 1,
      "not_found_count": 0,
      "total_requested": 4
    }
  }
}
```

### Get Product Statistics

```http
GET /api/products/statistics
```

**Response:**

```json
{
  "success": true,
  "message": "Product statistics retrieved successfully",
  "data": {
    "total_products": 156,
    "in_stock": 120,
    "low_stock": 25,
    "ordered": 8,
    "discontinued": 3,
    "total_value": 45678.99,
    "categories": ["Electronics", "Clothing", "Books", "Home & Garden"]
  }
}
```

### Get Low Stock Products

```http
GET /api/products/low-stock?threshold=10
```

**Response:**

```json
{
  "success": true,
  "message": "Low stock products retrieved successfully",
  "data": {
    "products": [
      {
        "id": 5,
        "name": "USB Cable",
        "quantity": 3,
        "category": "Electronics",
        "price": "12.99",
        "status": "low_stock"
      }
    ],
    "threshold": 10,
    "count": 1
  }
}
```

### Get Suggested Categories

```http
GET /api/products/suggested-categories
```

**Response:**

```json
{
  "success": true,
  "message": "Suggested categories retrieved successfully",
  "data": {
    "categories": ["Books", "Clothing", "Electronics", "Home & Garden"]
  }
}
```

### Check Product Name Availability

```http
POST /api/products/check-name
```

**Request Body:**

```json
{
  "name": "Wireless Mouse",
  "exclude_id": 5
}
```

**Response:**

```json
{
  "success": true,
  "message": "Name availability checked",
  "data": {
    "name": "Wireless Mouse",
    "available": false,
    "exists": true
  }
}
```

### Check Deletion Constraints

```http
POST /api/products/check-deletion-constraints
```

**Request Body:**

```json
{
  "ids": [1, 2, 3]
}
```

**Response:**

```json
{
  "success": true,
  "message": "Deletion constraints checked",
  "data": {
    "constraints": {
      "2": {
        "product_name": "Expensive Laptop",
        "constraints": [
          "High-value product (total value > $10,000)",
          "Last product in category \"Premium Electronics\""
        ]
      }
    },
    "can_delete_all": false,
    "constrained_count": 1,
    "total_checked": 3
  }
}
```

---

## 🤖 Chatbot API

### Send Message

```http
POST /api/sendMessage
```

**Request Body:**

```json
{
  "message": "How many products do we have in stock?"
}
```

---

## 📝 Validation Rules

### Product Creation/Update

- `name`: Required, string, max 255 characters, unique
- `quantity`: Required, integer, min 0
- `price`: Required, numeric, min 0, max 99,999,999.99
- `category`: Optional, string, max 255 characters
- `description`: Optional, string, max 1000 characters
- `status`: Optional, enum (in_stock, low_stock, ordered, discontinued)

### Product Filtering

- `search`: Optional, string, max 255 characters
- `category`: Optional, string, max 255 characters
- `status`: Optional, enum (in_stock, low_stock, ordered, discontinued)
- `min_price`/`max_price`: Optional, numeric, min 0
- `sort_by`: Optional, enum (name, price, quantity, created_at, updated_at)
- `sort_order`: Optional, enum (asc, desc)
- `per_page`: Optional, integer, min 1, max 100
- `page`: Optional, integer, min 1

### Bulk Deletion

- `ids`: Required, array, min 1 item, max 50 items
- `ids.*`: Required, integer, must exist in products table
- `force`: Optional, boolean

---

## 🔒 Error Handling

### Error Response Format

```json
{
  "success": false,
  "message": "Error description",
  "data": {
    "errors": {
      "field": ["Validation error message"]
    }
  }
}
```

### HTTP Status Codes

- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `404`: Not Found
- `422`: Validation Error
- `500`: Internal Server Error

---

## 🧪 Testing

```bash
# Backend tests
cd backend
php artisan test

# Frontend tests
cd frontend
npm run test
```

## 📊 Database Schema

### Products Table

| Column      | Type          | Description         |
| ----------- | ------------- | ------------------- |
| id          | bigint        | Primary key         |
| name        | varchar(255)  | Product name        |
| quantity    | int           | Stock quantity      |
| category    | varchar(255)  | Product category    |
| description | text          | Product description |
| price       | decimal(10,2) | Product price       |
| status      | enum          | Product status      |
| created_at  | timestamp     | Creation time       |
| updated_at  | timestamp     | Last update time    |

### Users Table

| Column     | Type         | Description      |
| ---------- | ------------ | ---------------- |
| id         | bigint       | Primary key      |
| name       | varchar(255) | User name        |
| email      | varchar(255) | User email       |
| password   | varchar(255) | Hashed password  |
| created_at | timestamp    | Creation time    |
| updated_at | timestamp    | Last update time |

## 🚀 Deployment

### Production Environment Variables

```bash
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=inventory_db
DB_USERNAME=your-username
DB_PASSWORD=your-password
JWT_SECRET=your-jwt-secret
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
