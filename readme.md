# Modular File Processing System

This project contains two backend services for managing file uploads and performing data transformations, all packaged with Docker Compose.

---

## 📦 Modules

### ✅ Module A – File Management Service (Laravel)
Handles:
- Uploading `.csv`, `.xlsx`, and `.json` files
- File validation (max 10MB, type check)
- Metadata storage (MySQL)
- Triggering Python microservice via queue
- Token-based secure API (currently used custom token but can implement it with laravel sanctum or jwt)
- Async job dispatch

### ✅ Module B – Data Transformation Microservice (FastAPI)
Handles:
- Receiving transformation job
- Parsing CSV, XLSX (headers, null %, duplicate %, column types)
- Parsing JSON (depth, key structure)
- Logs activity
- Stores result in `/results`
- Callback to PHP with result location

---

## 🚀 Setup

### 1. Clone Repository
```bash
git clone https://github.com/dev-abhishek-dixit/ModularFileProcessingSystem.git
cd ModularFileProcessingSystem
```

### 2. Environment Files
Create `.env` files for both services:

#### `php-service/.env`
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8001

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=file_manager
DB_USERNAME=root
DB_PASSWORD=root

API_TOKEN=your-secret-token (set any random token)
PYTHON_TRANSFORM_URL=http://python-service:8000/api/transform
```

#### `python-service/.env`
```env
API_TOKEN=your-secret-token (set same token as in php-service .env)
PHP_CALLBACK_URL=http://php-service/api/update-status
```

### 3. Start Docker Containers
Run the following command to build and start the containers:
```bash
docker-compose up --build
```

### 4. Laravel Setup (inside the running container)
Run the following commands to set up Laravel:
```bash
docker exec -it php-service bash
composer install
php artisan migrate
php artisan key:generate
php artisan queue:work
```

---

## 📂 Directory Structure
```plaintext
project-root/
│
├── php-service/          # Laravel app
│   ├── .env
│   └── /app/uploads/          # Mount for uploaded files
│
├── python-service/       # FastAPI app
│   ├── .env
│   └── results/          # Mount for transformation results
│
├── docker-compose.yml
├── .env.example
└── README.md
```

---

## 🔗 API Endpoints

### 📁 Laravel (php-service)
| Method | Endpoint               | Description                     |
|--------|-------------------------|---------------------------------|
| POST   | `/api/upload`          | Upload file                    |
| GET    | `/api/status/{file_id}`| Get file processing status      |
| POST   | `/api/update-status`   | Callback for processing result |

### 🧪 Python (python-service)
| Method | Endpoint     | Description                     |
|--------|--------------|---------------------------------|
| POST   | `api/transform` | Perform transformation and callback |

---

## 🧪 Sample Test (Upload a File)
Use the following `curl` command to test file upload:
```bash
curl -X POST http://localhost:8001/api/upload \
  -H "Authorization: Bearer your-secret-token" \
  -F "file=@/full/path/to/your-file.csv"
```