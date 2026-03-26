# Setup Microservices - Manual Guide

## Prerequisites
- MySQL 5.7+ running locally (port 3306)
- Go 1.21+ installed
- Terminal/CMD dengan akses ke Go dan MySQL commands

---

## Step 1: Create MySQL Databases

Buka MySQL client dan jalankan commands berikut:

```bash
mysql -u root -p
```

Kemudian jalankan SQL ini:

```sql
CREATE DATABASE IF NOT EXISTS meditrack_auth;
CREATE DATABASE IF NOT EXISTS meditrack_patient;
CREATE DATABASE IF NOT EXISTS meditrack_doctor;
CREATE DATABASE IF NOT EXISTS meditrack_appointment;
CREATE DATABASE IF NOT EXISTS meditrack_prescription;
CREATE DATABASE IF NOT EXISTS meditrack_pharmacy;
CREATE DATABASE IF NOT EXISTS meditrack_analytics;

-- Verify
SHOW DATABASES LIKE 'meditrack_%';
```

Atau gunakan satu command:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_auth; CREATE DATABASE IF NOT EXISTS meditrack_patient; CREATE DATABASE IF NOT EXISTS meditrack_doctor; CREATE DATABASE IF NOT EXISTS meditrack_appointment; CREATE DATABASE IF NOT EXISTS meditrack_prescription; CREATE DATABASE IF NOT EXISTS meditrack_pharmacy; CREATE DATABASE IF NOT EXISTS meditrack_analytics;"
```

---

## Step 2: Setup Environment Files

Setiap service membutuhkan `.env` file. Copy `.env.example` ke `.env` untuk masing-masing:

**Option A: Menggunakan PowerShell**

```powershell
cd "d:\semester 6\tugas\microservice\microservices"

foreach ($service in @("auth-service", "patient-service", "doctor-service", "appointment-service", "prescription-service", "pharmacy-service")) {
    cd $service
    if (!(Test-Path ".env")) {
        Copy-Item ".env.example" ".env"
        Write-Host "✓ Created .env for $service"
    }
    cd ..
}
```

**Option B: Manual untuk tiap service**

```bash
cd d:\semester 6\tugas\microservice\microservices\auth-service
copy .env.example .env

cd d:\semester 6\tugas\microservice\microservices\patient-service
copy .env.example .env

# ... repeat untuk service lainnya
```

---

## Step 3: Install Go Dependencies

Jalankan untuk setiap service:

```bash
# Auth Service
cd d:\semester 6\tugas\microservice\microservices\auth-service
go mod download
go mod tidy

# Patient Service
cd d:\semester 6\tugas\microservice\microservices\patient-service
go mod download
go mod tidy

# Doctor Service
cd d:\semester 6\tugas\microservice\microservices\doctor-service
go mod download
go mod tidy

# Appointment Service
cd d:\semester 6\tugas\microservice\microservices\appointment-service
go mod download
go mod tidy

# Prescription Service
cd d:\semester 6\tugas\microservice\microservices\prescription-service
go mod download
go mod tidy

# Pharmacy Service
cd d:\semester 6\tugas\microservice\microservices\pharmacy-service
go mod download
go mod tidy
```

### Verifikasi Go Modules

```bash
# Check if modules are properly setup
go mod verify
```

---

## Step 4: Run Migrations & Seeders

Mana-mana service secara otomatis will:
1. Load .env configuration
2. Connect to MySQL
3. Run migrations (create tables)
4. Seed sample data
5. Start HTTP server

### Option A: Automated (Run All Services)

```bash
cd d:\semester 6\tugas\microservice
.\start-all-services.bat
```

### Option B: Manual (Run Each Service Separately)

Buka 6 terminal windows, jalankan masing-masing:

**Terminal 1 - Auth Service (Port 8001)**
```bash
cd d:\semester 6\tugas\microservice\microservices\auth-service
go run main.go
```

**Terminal 2 - Patient Service (Port 8002)**
```bash
cd d:\semester 6\tugas\microservice\microservices\patient-service
go run main.go
```

**Terminal 3 - Doctor Service (Port 8003)**
```bash
cd d:\semester 6\tugas\microservice\microservices\doctor-service
go run main.go
```

**Terminal 4 - Appointment Service (Port 8004)**
```bash
cd d:\semester 6\tugas\microservice\microservices\appointment-service
go run main.go
```

**Terminal 5 - Prescription Service (Port 8005)**
```bash
cd d:\semester 6\tugas\microservice\microservices\prescription-service
go run main.go
```

**Terminal 6 - Pharmacy Service (Port 8006)**
```bash
cd d:\semester 6\tugas\microservice\microservices\pharmacy-service
go run main.go
```

Expected output untuk setiap service:
```
Auth Service Database connected successfully!
Running migrations...
Seeding auth users...
Server running on :8001
```

---

## Step 5: Verify Services are Running

Buka terminal baru dan test setiap service:

```bash
# Test Auth Service
curl http://localhost:8001/api/health

# Test Patient Service
curl http://localhost:8002/api/health

# Test Doctor Service
curl http://localhost:8003/api/health

# Test Appointment Service
curl http://localhost:8004/api/health

# Test Prescription Service
curl http://localhost:8005/api/health

# Test Pharmacy Service
curl http://localhost:8006/api/health
```

Expected response:
```json
{"status":"Healthcare Service is running"}
```

---

## Step 6: Test API Endpoints

### Login (Auth Service)

```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"admin@meditrack.com\",\"password\":\"password\"}"
```

Response:
```json
{
  "id": 1,
  "name": "Admin User",
  "email": "admin@meditrack.com",
  "role": "admin",
  "token": "eyJhbG..."
}
```

### Get Patients (dengan token dari login)

```bash
TOKEN="YOUR_TOKEN_HERE"
curl -X GET http://localhost:8002/api/patients \
  -H "Authorization: Bearer $TOKEN"
```

---

## Troubleshooting

### "Connection refused" or "Cannot connect to database"
- MySQL tidak berjalan: Start MySQL service
- Database tidak ada: Run Step 1 (Create databases)
- .env credentials salah: Edit .env dengan MySQL credentials yang benar

### "missing go.sum entry"
```bash
cd microservices/auth-service
go mod tidy
go mod download
go mod verify
```

### "Port already in use"
```bash
# Windows - Find process using port
netstat -ano | findstr :8001

# Kill process
taskkill /PID <PID> /F
```

### "Invalid token" or "Unauthorized"
- Pastikan token dari auth service digunakan
- Format: `Authorization: Bearer <token>`
- Gunakan token yang fresh (tidak expired)

---

## Demo Credentials

Admin:
```
Email: admin@meditrack.com
Password: password
```

Doctor:
```
Email: dr.suryadi@meditrack.com
Password: password
```

Patient:
```
Email: ahmad.wijaya@example.com
Password: password
```

---

## Database Verification

Untuk verify migrations berhasil:

```bash
mysql -u root -p

# In MySQL client:
USE meditrack_auth;
SHOW TABLES;
SELECT COUNT(*) FROM users;

USE meditrack_patient;
SHOW TABLES;
SELECT COUNT(*) FROM patients;

# etc...
```

---

## Next Steps

1. ✅ Databases created
2. ✅ Dependencies installed (go mod download)
3. ✅ Migrations ran (GORM AutoMigrate)
4. ✅ Seeders ran (demo data inserted)
5. ⏭️ **Update Laravel controllers** to call Go APIs
   - See: LARAVEL_INTEGRATION_GUIDE.md
6. ⏭️ **Test end-to-end flows** (Laravel → Go APIs)
7. ⏭️ **Deploy** to production

---

## Quick Reference Commands

```bash
# Check Go version
go version

# Check MySQL
mysql --version

# Check MySQL connection
mysql -u root -p -e "SELECT 1"

# Install all dependencies (run in root dir)
for /d %S in (microservices\*-service) do (
    cd %S
    go mod download
    go mod tidy
    cd ..\..
)

# Test all services running
for /L %I in (1,1,6) do curl http://localhost:800%I/api/health

# View logs (if running in foreground)
# Watch the terminal output as services start
```

---

## Success Indicators

✅ You're done when:
- [ ] All 6 services show "Server running on :800X"
- [ ] All /health endpoints return status
- [ ] MySQL databases contain tables and seed data
- [ ] Can login with admin@meditrack.com
- [ ] Can fetch patients/doctors/appointments via APIs

---

**Need help?** Refer to:
- API_ENDPOINTS_REFERENCE.md - API documentation
- MICROSERVICES_SUMMARY.md - Quick reference
- SETUP_VERIFICATION_CHECKLIST.md - Full checklist
