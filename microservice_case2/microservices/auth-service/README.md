# Auth Service - MediTrack Microservice

Layanan autentikasi untuk MediTrack - Healthcare Management System

## Deskripsi
Auth Service menangani:
- User registration dan login
- JWT token generation dan validation
- User profile management
- Role-based access control

## Setup

### 1. Install Dependencies
```bash
cd microservices/auth-service
go mod download
```

### 2. Konfigurasi Database
```bash
# Copy .env.example ke .env
cp .env.example .env

# Edit .env dan sesuaikan konfigurasi database
```

### 3. Jalankan Service
```bash
go run main.go
```

Service akan berjalan di: `http://localhost:8001`

## API Endpoints

### Authentication
- `POST /api/auth/login` - Login user
- `POST /api/auth/register` - Register user baru
- `POST /api/auth/verify-token` - Verify JWT token

### Profile (Requires Authentication)
- `GET /api/profile` - Get current user profile
- `PUT /api/profile` - Update current user profile

### Users (Requires Authentication)
- `GET /api/users/:id` - Get user by ID

## Demo Accounts

### Admin
- Email: admin@meditrack.com
- Password: password
- Role: admin

### Doctors
- Email: doctor1@meditrack.com - doctor4@meditrack.com
- Password: password (semua)
- Role: doctor

### Pharmacists
- Email: pharmacist1@meditrack.com - pharmacist3@meditrack.com
- Password: password (semua)
- Role: pharmacist

### Patients
- Email: patient1@meditrack.com - patient8@meditrack.com
- Password: password (semua)
- Role: patient
