# Patient Service - MediTrack Microservice

Layanan manajemen pasien untuk MediTrack - Healthcare Management System

## Deskripsi

Patient Service menangani:
- Patient profile management
- Electronic Health Records (EHR)
- Lab results tracking
- Patient outcomes

## Setup

### 1. Install Dependencies
```bash
cd microservices/patient-service
go mod download
```

### 2. Konfigurasi Database
```bash
cp .env.example .env
# Edit .env sesuai konfigurasi
```

### 3. Jalankan Service
```bash
go run main.go
```

Service akan berjalan di: `http://localhost:8002`

## API Endpoints

### Patient Management (Requires Authentication)
- `POST /api/patients` - Create patient
- `GET /api/patients` - Get all patients (with pagination)
- `GET /api/patients/:id` - Get patient by ID
- `PUT /api/patients/:id` - Update patient
- `DELETE /api/patients/:id` - Delete patient
- `GET /api/patients/email/:email` - Get patient by email
- `GET /api/patients/search?q=keyword` - Search patients

### Electronic Health Records (Requires Authentication)
- `POST /api/patients/:patientID/ehr` - Create EHR
- `GET /api/patients/:patientID/ehr` - Get patient EHRs
- `GET /api/patients/ehr/:id` - Get specific EHR
- `PUT /api/patients/ehr/:id` - Update EHR
- `DELETE /api/patients/ehr/:id` - Delete EHR

### Lab Results (Requires Authentication)
- `POST /api/patients/:patientID/lab-results` - Create lab result
- `GET /api/patients/:patientID/lab-results` - Get patient lab results
- `GET /api/patients/lab-results/:id` - Get specific lab result
- `PUT /api/patients/lab-results/:id` - Update lab result
- `DELETE /api/patients/lab-results/:id` - Delete lab result
- `POST /api/patients/lab-results/:id/complete` - Complete lab result

