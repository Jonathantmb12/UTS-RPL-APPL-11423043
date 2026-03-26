# Complete Setup & Verification Checklist

## ✅ Phase 1: Prerequisites

- [ ] Go 1.21+ installed (`go version`)
- [ ] PHP 8.1+ installed (`php --version`)
- [ ] MySQL 5.7+ running (`mysql --version`)
- [ ] Composer installed (`composer --version`)
- [ ] Node.js/npm installed (`npm --version`)

---

## ✅ Phase 2: Database Setup

Open MySQL client and run:

```sql
CREATE DATABASE IF NOT EXISTS meditrack_auth;
CREATE DATABASE IF NOT EXISTS meditrack_patient;
CREATE DATABASE IF NOT EXISTS meditrack_doctor;
CREATE DATABASE IF NOT EXISTS meditrack_appointment;
CREATE DATABASE IF NOT EXISTS meditrack_prescription;
CREATE DATABASE IF NOT EXISTS meditrack_pharmacy;
CREATE DATABASE IF NOT EXISTS meditrack_analytics;
```

**Verification:**
```bash
mysql -u root -p -e "SHOW DATABASES LIKE 'meditrack_%';"
```

Expected output: 7 databases listed

- [ ] All 7 databases created

---

## ✅ Phase 3: Environment Files

For each service, copy `.env.example` to `.env`:

```bash
cd microservices/auth-service && cp .env.example .env
cd ../patient-service && cp .env.example .env
cd ../doctor-service && cp .env.example .env
cd ../appointment-service && cp .env.example .env
cd ../prescription-service && cp .env.example .env
cd ../pharmacy-service && cp .env.example .env
```

**Update each `.env` file with your MySQL credentials:**
```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=your_password
DB_PORT=3306
```

- [ ] Auth Service .env created & configured
- [ ] Patient Service .env created & configured
- [ ] Doctor Service .env created & configured
- [ ] Appointment Service .env created & configured
- [ ] Prescription Service .env created & configured
- [ ] Pharmacy Service .env created & configured

---

## ✅ Phase 4: Start Go Services

**Option A: Manual Start (Recommended for Testing)**

Open 6 terminal windows and run each:

```bash
# Terminal 1 - Auth Service
cd microservices/auth-service
go mod download
go run main.go
# Should show: Server running on :8001

# Terminal 2 - Patient Service
cd microservices/patient-service
go mod download
go run main.go
# Should show: Server running on :8002

# Terminal 3 - Doctor Service
cd microservices/doctor-service
go mod download
go run main.go
# Should show: Server running on :8003

# Terminal 4 - Appointment Service
cd microservices/appointment-service
go mod download
go run main.go
# Should show: Server running on :8004

# Terminal 5 - Prescription Service
cd microservices/prescription-service
go mod download
go run main.go
# Should show: Server running on :8005

# Terminal 6 - Pharmacy Service
cd microservices/pharmacy-service
go mod download
go run main.go
# Should show: Server running on :8006
```

**Option B: Automated Start (Windows)**
```bash
start-all-services.bat
```

**Option C: Automated Start (Linux/Mac)**
```bash
chmod +x start-all-services.sh
./start-all-services.sh
```

**Verification:** Check each service is running
```bash
# In a new terminal
curl http://localhost:8001/api/health
curl http://localhost:8002/api/health
curl http://localhost:8003/api/health
curl http://localhost:8004/api/health
curl http://localhost:8005/api/health
curl http://localhost:8006/api/health
```

Expected response: `{"status":"healthy"}`

- [ ] Auth Service running on :8001
- [ ] Patient Service running on :8002
- [ ] Doctor Service running on :8003
- [ ] Appointment Service running on :8004
- [ ] Prescription Service running on :8005
- [ ] Pharmacy Service running on :8006

---

## ✅ Phase 5: Laravel Setup

```bash
# Install dependencies
composer install

# Copy .env
cp .env.example .env

# Generate app key
php artisan key:generate

# Run Laravel server
php artisan serve
# Should show: Server running on http://localhost:8000
```

- [ ] Laravel dependencies installed
- [ ] Laravel .env configured
- [ ] Laravel app key generated
- [ ] Laravel running on :8000

---

## ✅ Phase 6: Verify API Endpoints

### 6.1 Test Auth Service

**Login Test:**
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@meditrack.com","password":"password"}'
```

Expected response:
```json
{
  "id": 1,
  "name": "Admin User",
  "email": "admin@meditrack.com",
  "role": "admin",
  "token": "eyJhbG..."
}
```

Save the `token` value for testing other services.

- [ ] Login successful with token

**Get Profile Test:**
```bash
curl -X GET http://localhost:8001/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Expected: User profile returned

- [ ] Get profile successful

### 6.2 Test Patient Service

**List Patients:**
```bash
curl -X GET http://localhost:8002/api/patients \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Expected: Patient list with data

- [ ] List patients successful

**Get One Patient:**
```bash
curl -X GET http://localhost:8002/api/patients/2 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Expected: Single patient details

- [ ] Get single patient successful

### 6.3 Test Doctor Service

**List Doctors:**
```bash
curl -X GET http://localhost:8003/api/doctors \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Expected: Doctor list

- [ ] List doctors successful

**Get Doctor Performance:**
```bash
curl -X GET http://localhost:8003/api/doctors/3/performance \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Expected: Performance metrics

- [ ] Get doctor performance successful

### 6.4 Test Appointment Service

**Get Available Slots:**
```bash
curl -X GET "http://localhost:8004/api/doctors/3/available-slots?date=2024-03-26" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Expected: List of available time slots

- [ ] Get available slots successful

**Create Appointment:**
```bash
curl -X POST http://localhost:8004/api/appointments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 2,
    "doctor_id": 3,
    "appointment_date": "2024-03-26 14:00:00",
    "reason_for_visit": "Chest pain"
  }'
```

Expected: Appointment created with ID

- [ ] Create appointment successful

### 6.5 Test Prescription Service

**Create Prescription:**
```bash
curl -X POST http://localhost:8005/api/prescriptions \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 2,
    "doctor_id": 3,
    "medication_name": "Aspirin",
    "dosage": "500mg",
    "frequency": "Twice daily",
    "quantity": 60,
    "duration_days": 30
  }'
```

Expected: Prescription created

- [ ] Create prescription successful

### 6.6 Test Pharmacy Service

**List Inventory:**
```bash
curl -X GET http://localhost:8006/api/inventory \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Expected: Inventory items list

- [ ] List inventory successful

**Create Payment:**
```bash
curl -X POST http://localhost:8006/api/payments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 2,
    "prescription_id": 5,
    "amount": 50.00,
    "payment_method": "card"
  }'
```

Expected: Payment created with receipt number

- [ ] Create payment successful

---

## ✅ Phase 7: Test Laravel Integration

### 7.1 Create Service Client

Create `app/Services/MicroserviceClient.php` (code provided in LARAVEL_INTEGRATION_GUIDE.md)

- [ ] MicroserviceClient created
- [ ] All HTTP methods implemented (get, post, put, delete)

### 7.2 Update Controllers

Update controllers to use MicroserviceClient:
- [ ] AuthController updated
- [ ] PatientController updated
- [ ] DoctorController updated
- [ ] AppointmentController updated
- [ ] PrescriptionController updated
- [ ] PharmacyDetailController updated

### 7.3 Test Laravel Routes

```bash
# Visit Laravel UI
http://localhost:8000

# Test login
POST /login with email: admin@meditrack.com, password: password

# Should redirect to /dashboard

# Test patient list
GET /patients

# Should display patients from Go service
```

- [ ] Laravel login successful
- [ ] Token stored in session
- [ ] Patient list displays from Go API
- [ ] Create patient form works

---

## ✅ Phase 8: Database Verification

Verify seeded data in each database:

```bash
# Check Auth users
mysql meditrack_auth -u root -p -e "SELECT id, email, role FROM users LIMIT 5;"

# Check Patients
mysql meditrack_patient -u root -p -e "SELECT id, name, email FROM patients LIMIT 5;"

# Check Doctors
mysql meditrack_doctor -u root -p -e "SELECT id, name, specialization FROM doctors LIMIT 5;"

# Check Appointments
mysql meditrack_appointment -u root -p -e "SELECT id, patient_id, doctor_id, appointment_date FROM appointments LIMIT 5;"

# Check Prescriptions
mysql meditrack_prescription -u root -p -e "SELECT id, patient_id, medication_name FROM prescriptions LIMIT 5;"

# Check Pharmacy Inventory
mysql meditrack_pharmacy -u root -p -e "SELECT id, medication_name, stock_quantity FROM pharmacy_inventories LIMIT 5;"
```

- [ ] Auth Service has seeded users
- [ ] Patient Service has seeded patients
- [ ] Doctor Service has seeded doctors
- [ ] Appointment Service has test appointments
- [ ] Prescription Service has test prescriptions
- [ ] Pharmacy Service has inventory items

---

## ✅ Phase 9: Error Handling

Test error scenarios:

```bash
# Test invalid token
curl -X GET http://localhost:8002/api/patients \
  -H "Authorization: Bearer invalid_token"
# Should return 401 Unauthorized

# Test missing authorization
curl -X GET http://localhost:8002/api/patients
# Should return 401 Unauthorized

# Test invalid endpoint
curl -X GET http://localhost:8002/api/invalid
# Should return 404 Not Found

# Test invalid POST data
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@meditrack.com"}'
# Should return 400 Bad Request
```

- [ ] 401 errors handled properly
- [ ] 404 errors handled properly
- [ ] 400 validation errors handled properly
- [ ] CORS headers present in responses

---

## ✅ Phase 10: Performance & Logging

### Monitor Services

Open each service terminal and check:
- Services startup without errors
- No memory leaks (memory usage stable)
- Database connections pooled correctly

### Check Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Go services (check in service output window)
# Should show HTTP requests being processed
```

- [ ] No startup errors in any service
- [ ] Requests logged properly
- [ ] No panic errors in Go services

---

## ✅ Phase 11: Documentation Review

Verify all documentation is accessible:

- [ ] MICROSERVICES_IMPLEMENTATION_GUIDE.md exists
- [ ] MICROSERVICES_SUMMARY.md exists
- [ ] API_ENDPOINTS_REFERENCE.md exists
- [ ] LARAVEL_INTEGRATION_GUIDE.md exists
- [ ] start-all-services.bat/sh created

---

## 🎉 Final Verification

If all checkboxes are checked:

✅ **You have a fully functional microservices healthcare system!**

### What's Working:
- 6 independent Go microservices with separate databases
- Full JWT authentication across services
- Complete CRUD operations for all entities
- Laravel UI calling Go service APIs
- Seeded demo data (51+ records)
- Error handling and validation
- CORS support

### Ready Next Steps:
1. **Migrate remaining Laravel controllers** - Use provided examples
2. **Add more services** - Analytics Service template provided
3. **Deploy to Docker** - Create Dockerfile for each service
4. **Add monitoring** - Prometheus, ELK Stack, etc.
5. **Implement CI/CD** - GitHub Actions, GitLab CI
6. **Scale services** - Load balancing, Kubernetes

---

## 🆘 Troubleshooting

### Port Already in Use
```bash
# Find process using port
lsof -i :8001  # Linux/Mac
netstat -ano | findstr :8001  # Windows

# Kill process
kill -9 PID  # Linux/Mac
taskkill /PID PID /F  # Windows
```

### Database Connection Failed
- Check MySQL running: `mysql -u root -p`
- Verify credentials in .env
- Check database exists: `SHOW DATABASES;`

### Token Issues
- Clear session/cookies in browser
- Get fresh token from login
- Check Authorization header format: `Bearer TOKEN`

### CORS Errors
- All services have CORS configured
- Check browser console for specific error
- Verify Laravel is on localhost:8000

### Go Compilation Error
- Ensure Go 1.21+: `go version`
- Run `go mod tidy` in service directory
- Delete go.sum and `go mod download`

---

## 📞 Quick Support Commands

```bash
# Check all services running
for port in 8001 8002 8003 8004 8005 8006; do
  curl -s http://localhost:$port/api/health || echo "Port $port down"
done

# Get auth token
TOKEN=$(curl -s -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@meditrack.com","password":"password"}' | jq '.token')
echo $TOKEN

# Test service with token
curl -H "Authorization: Bearer $TOKEN" http://localhost:8002/api/patients?page=1

# Check database size
mysql -u root -p -e "
  SELECT table_schema, ROUND(SUM(data_length+index_length)/1024/1024, 2) AS size_mb
  FROM information_schema.tables
  WHERE table_schema LIKE 'meditrack_%'
  GROUP BY table_schema;
"
```

---

**Date Completed:** _______________

**Verified By:** _______________

**Status:** ✅ READY FOR DEVELOPMENT
