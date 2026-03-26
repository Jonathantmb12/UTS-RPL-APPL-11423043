# 🚀 Panduan Setup Lengkap Microservices

**Tanggal**: 26 Maret 2026  
**Status**: ✅ Siap Digunakan

---

## 📋 Checklist Setup

Ikuti langkah-langkah berikut secara berurutan:

### ✅ Tahap 1: Persiapan (5 menit)

**Pastikan tersedia:**
- [ ] MySQL 5.7+ sudah terinstall
- [ ] Go 1.21+ sudah terinstall  
- [ ] Terminal/CMD access
- [ ] Internet koneksi

**Verifikasi:**
```bash
go version
mysql --version
```

---

### ✅ Tahap 2: Buat Database MySQL (2 menit)

**PILIH SATU:**

#### Cara A: PowerShell Script (OTOMATIS - Recommended)
```powershell
cd "d:\semester 6\tugas\microservice"
.\setup-all.ps1
```
Script ini akan:
- ✓ Buat 7 databases
- ✓ Setup .env files
- ✓ Install Go dependencies
- ✓ Build semua services

#### Cara B: MySQL Commands (MANUAL)
```bash
# Buka MySQL client
mysql -u root -p

# Jalankan SQL ini:
```
```sql
CREATE DATABASE IF NOT EXISTS meditrack_auth;
CREATE DATABASE IF NOT EXISTS meditrack_patient;
CREATE DATABASE IF NOT EXISTS meditrack_doctor;
CREATE DATABASE IF NOT EXISTS meditrack_appointment;
CREATE DATABASE IF NOT EXISTS meditrack_prescription;
CREATE DATABASE IF NOT EXISTS meditrack_pharmacy;
CREATE DATABASE IF NOT EXISTS meditrack_analytics;

SHOW DATABASES LIKE 'meditrack_%';
```
```bash
# Atau single command
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_auth; CREATE DATABASE IF NOT EXISTS meditrack_patient; CREATE DATABASE IF NOT EXISTS meditrack_doctor; CREATE DATABASE IF NOT EXISTS meditrack_appointment; CREATE DATABASE IF NOT EXISTS meditrack_prescription; CREATE DATABASE IF NOT EXISTS meditrack_pharmacy; CREATE DATABASE IF NOT EXISTS meditrack_analytics;"
```

**Verifikasi:**
```bash
mysql -u root -p -e "SHOW DATABASES LIKE 'meditrack_%';"
```

Harus muncul 7 databases: ✓

---

### ✅ Tahap 3: Copy .env Files (1 menit)

Setiap service perlu `.env` configuration file.

**PowerShell (Otomatis):**
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

**Manual (Tiap service):**
```bash
cd d:\semester 6\tugas\microservice\microservices\auth-service
copy .env.example .env

cd d:\semester 6\tugas\microservice\microservices\patient-service
copy .env.example .env

# ... repeat untuk 4 service lainnya
```

**Verifikasi:**
```bash
cd d:\semester 6\tugas\microservice\microservices\auth-service
ls -la | grep .env
```

---

### ✅ Tahap 4: Install Go Dependencies (3-5 menit)

Jalankan untuk **SETIAP service**:

```bash
# AUTH SERVICE
cd d:\semester 6\tugas\microservice\microservices\auth-service
go mod download
go mod tidy
go mod verify

# PATIENT SERVICE
cd d:\semester 6\tugas\microservice\microservices\patient-service
go mod download
go mod tidy
go mod verify

# DOCTOR SERVICE
cd d:\semester 6\tugas\microservice\microservices\doctor-service
go mod download
go mod tidy
go mod verify

# APPOINTMENT SERVICE
cd d:\semester 6\tugas\microservice\microservices\appointment-service
go mod download
go mod tidy
go mod verify

# PRESCRIPTION SERVICE
cd d:\semester 6\tugas\microservice\microservices\prescription-service
go mod download
go mod tidy
go mod verify

# PHARMACY SERVICE
cd d:\semester 6\tugas\microservice\microservices\pharmacy-service
go mod download
go mod tidy
go mod verify
```

**Apa yang ini lakukan:**
- `go mod download` → Download semua packages
- `go mod tidy` → Update go.mod dan go.sum
- `go mod verify` → Verify integrity

**Verifikasi:**
```bash
# Check go.sum ada
ls -la go.sum

# Check dapat di-compile
go build -o test.exe main.go
```

---

### ✅ Tahap 5: Jalankan Services (1 menit setup + ongoing)

**PILIH SATU:**

#### Cara A: Otomatis (RECOMMENDED)

```bash
cd d:\semester 6\tugas\microservice
.\start-all-services.bat
```

Ini akan buka 7 terminal windows baru dengan:
- 6 Microservices (ports 8001-8006)
- 1 Laravel UI (port 8000)

#### Cara B: Manual (Per Terminal)

Buka **6 terminal terpisah**, dan run di masing-masing:

```bash
# TERMINAL 1 - Auth Service (Port 8001)
cd d:\semester 6\tugas\microservice\microservices\auth-service && go run main.go
# Output: Server running on :8001

# TERMINAL 2 - Patient Service (Port 8002)
cd d:\semester 6\tugas\microservice\microservices\patient-service && go run main.go
# Output: Server running on :8002

# TERMINAL 3 - Doctor Service (Port 8003)
cd d:\semester 6\tugas\microservice\microservices\doctor-service && go run main.go
# Output: Server running on :8003

# TERMINAL 4 - Appointment Service (Port 8004)
cd d:\semester 6\tugas\microservice\microservices\appointment-service && go run main.go
# Output: Server running on :8004

# TERMINAL 5 - Prescription Service (Port 8005)
cd d:\semester 6\tugas\microservice\microservices\prescription-service && go run main.go
# Output: Server running on :8005

# TERMINAL 6 - Pharmacy Service (Port 8006)
cd d:\semester 6\tugas\microservice\microservices\pharmacy-service && go run main.go
# Output: Server running on :8006
```

**Expected output untuk setiap service:**
```
Auth Service Database connected successfully!
Running migrations...
Seeding database with sample data...
Server running on :8001
```

Jika melihat "Server running on :" = ✓ Service siap!

---

### ✅ Tahap 6: Verifikasi Berjalan (2 menit)

**Test Health Endpoints:**

```bash
# Test semua services
curl http://localhost:8001/api/health
curl http://localhost:8002/api/health
curl http://localhost:8003/api/health
curl http://localhost:8004/api/health
curl http://localhost:8005/api/health
curl http://localhost:8006/api/health
```

**Expected Response:**
```json
{"status":"Auth Service is running"}
```

Jika semua respond = ✓ All services running!

---

### ✅ Tahap 7: Test Login (1 menit)

```bash
# Login API test
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@meditrack.com","password":"password"}'
```

**Expected Response:**
```json
{
  "id": 1,
  "name": "Admin User",
  "email": "admin@meditrack.com",
  "role": "admin",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

Jika dapat token = ✓ Authentication working!

---

### ✅ Tahap 8: Verifikasi Database (2 menit)

```bash
# Check tables dibuat
mysql -u root -p meditrack_auth -e "SHOW TABLES;"
mysql -u root -p meditrack_patient -e "SHOW TABLES;"
mysql -u root -p meditrack_doctor -e "SHOW TABLES;"

# Check data di-seed
mysql -u root -p meditrack_auth -e "SELECT COUNT(*) as user_count FROM users;"
mysql -u root -p meditrack_patient -e "SELECT COUNT(*) as patient_count FROM patients;"
mysql -u root -p meditrack_doctor -e "SELECT COUNT(*) as doctor_count FROM doctors;"
```

**Expected:**
```
user_count: 16 (1 admin, 4 doctors, 3 pharmacists, 8 patients)
patient_count: 8
doctor_count: 4
```

---

## 🎓 Akun Demo (Pre-seeded)

Gunakan untuk login:

```
┌────────────────────────────────────┐
│ ADMIN                              │
├────────────────────────────────────┤
│ Email: admin@meditrack.com         │
│ Password: password                 │
└────────────────────────────────────┘

┌────────────────────────────────────┐
│ DOCTOR 1 (Cardiology)              │
├────────────────────────────────────┤
│ Email: dr.suryadi@meditrack.com    │
│ Password: password                 │
└────────────────────────────────────┘

┌────────────────────────────────────┐
│ PATIENT 1                          │
├────────────────────────────────────┤
│ Email: ahmad.wijaya@example.com    │
│ Password: password                 │
└────────────────────────────────────┘

┌────────────────────────────────────┐
│ PHARMACIST                         │
├────────────────────────────────────┤
│ Email: farma.unggul@meditrack.com  │
│ Password: password                 │
└────────────────────────────────────┘
```

---

## ⚠️ Troubleshooting

### "MySQL: Access denied for user 'root'"
```
✓ Solution:
1. Pastikan MySQL running
2. Check MySQL password benar
3. Update .env files dengan correct credentials
```

### "error downloading: unknown revision"
```
✓ Solution:
1. go clean -modcache
2. go mod download all
3. go mod tidy
```

### "port 8001 already in use"
```
✓ Solution:
# Windows - Find & kill process
netstat -ano | findstr :8001
taskkill /PID <PID> /F

# Then restart service
go run main.go
```

### "cannot find module declaring package"
```
✓ Solution:
cd service-directory
go mod tidy
go mod download
```

---

## 📊 Services Overview

| Service | Port | Database | Purpose |
|---------|------|----------|---------|
| Auth | 8001 | meditrack_auth | JWT authentication |
| Patient | 8002 | meditrack_patient | Patient management |
| Doctor | 8003 | meditrack_doctor | Doctor profiles |
| Appointment | 8004 | meditrack_appointment | Scheduling |
| Prescription | 8005 | meditrack_prescription | Prescriptions |
| Pharmacy | 8006 | meditrack_pharmacy | Inventory |

---

## 📚 Dokumentasi

| File | Tujuan |
|------|--------|
| SETUP_MANUAL_GUIDE.md | Step-by-step detail |
| API_ENDPOINTS_REFERENCE.md | API documentation |
| LARAVEL_INTEGRATION_GUIDE.md | Laravel integration |
| PROJECT_OVERVIEW.md | Architecture overview |
| QUICK_START.md | Original quick reference |

---

## ✅ Success Checklist

- [ ] Go dan MySQL terinstall
- [ ] 7 databases dibuat
- [ ] .env files dikopy untuk semua services
- [ ] Go dependencies di-install (go mod download)
- [ ] Semua services berjalan (go run main.go)
- [ ] Health endpoints respond (localhost:8001-8006)
- [ ] Login berhasil dengan credentials
- [ ] Database tables populated (SHOW TABLES)
- [ ] Sample data ada (SELECT COUNT(*))

Jika semua ✓ = **Ready for Development!**

---

## 🚀 Next Steps

1. **Integrate Laravel** → LARAVEL_INTEGRATION_GUIDE.md
2. **Test Endpoints** → API_ENDPOINTS_REFERENCE.md
3. **Verify System** → SETUP_VERIFICATION_CHECKLIST.md

---

**Selamat! 🎉 Microservices Anda siap digunakan!**
