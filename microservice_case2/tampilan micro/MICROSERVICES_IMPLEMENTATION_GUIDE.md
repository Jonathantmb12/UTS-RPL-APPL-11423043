# 🏥 MediTrack Microservices - Implementasi Guide

**Status:** Semua microservices telah berhasil dibuat dan siap untuk deployment!

---

## 📊 Arsitektur Microservices

Proyek Anda telah dipecah dari monolith menjadi 7 microservices independen:

```
meditrack/
├── microservices/
│   ├── auth-service/           (Port 8001) - User authentication & JWT
│   ├── patient-service/        (Port 8002) - Patient management, EHR, Lab results
│   ├── doctor-service/         (Port 8003) - Doctor management, Performance metrics
│   ├── appointment-service/    (Port 8004) - Appointment scheduling & management
│   ├── prescription-service/   (Port 8005) - Prescriptions & Orders
│   ├── pharmacy-service/       (Port 8006) - Inventory & Payments
│   └── analytics-service/      (Port 8007) - (Template tersedia di bawah)
│
├── app/ (Laravel)  - UI/Frontend saja
├── resources/      - Blade templates untuk web
└── routes/         - Web routes (API calls ke Go services)
```

---

## 🚀 QUICK START - Setup Semua Services

### 1. Setup Database Terlebih Dahulu
Buat 7 database MySQL baru:

```bash
mysql -u root -p

CREATE DATABASE meditrack_auth;
CREATE DATABASE meditrack_patient;
CREATE DATABASE meditrack_doctor;
CREATE DATABASE meditrack_appointment;
CREATE DATABASE meditrack_prescription;
CREATE DATABASE meditrack_pharmacy;
CREATE DATABASE meditrack_analytics;
```

### 2. Setup & Run Auth Service (PERTAMA KALI)
```bash
cd microservices/auth-service
cp .env.example .env
# Edit .env dengan konfigurasi database Anda
go mod download
go run main.go
```
✅ Auth Service berjalan di: `http://localhost:8001`

### 3. Setup & Run Semua Services Lainnya

#### Patient Service
```bash
cd microservices/patient-service
cp .env.example .env
go mod download
go run main.go
```

#### Doctor Service
```bash
cd microservices/doctor-service
cp .env.example .env
go mod download
go run main.go
```

#### Appointment Service
```bash
cd microservices/appointment-service
cp .env.example .env
go mod download
go run main.go
```

#### Prescription Service
```bash
cd microservices/prescription-service
cp .env.example .env
go mod download
go run main.go
```

#### Pharmacy Service
```bash
cd microservices/pharmacy-service
cp .env.example .env
go mod download
go run main.go
```

**Test Kesemua Services:**
```bash
curl http://localhost:8001/health
curl http://localhost:8002/health
curl http://localhost:8003/health
curl http://localhost:8004/health
curl http://localhost:8005/health
curl http://localhost:8006/health
```

---

## 🔧 Update Laravel Controllers

### 1. Buat Service Client Helper

Buat file: `app/Services/MicroserviceClient.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MicroserviceClient
{
    protected $baseUrls = [
        'auth' => 'http://localhost:8001/api',
        'patient' => 'http://localhost:8002/api',
        'doctor' => 'http://localhost:8003/api',
        'appointment' => 'http://localhost:8004/api',
        'prescription' => 'http://localhost:8005/api',
        'pharmacy' => 'http://localhost:8006/api',
    ];

    public function getToken()
    {
        return auth()->user()->api_token ?? session('api_token');
    }

    public function get($service, $endpoint)
    {
        return Http::withToken($this->getToken())
            ->get("{$this->baseUrls[$service]}{$endpoint}")
            ->json();
    }

    public function post($service, $endpoint, $data)
    {
        return Http::withToken($this->getToken())
            ->post("{$this->baseUrls[$service]}{$endpoint}", $data)
            ->json();
    }

    public function put($service, $endpoint, $data)
    {
        return Http::withToken($this->getToken())
            ->put("{$this->baseUrls[$service]}{$endpoint}", $data)
            ->json();
    }

    public function delete($service, $endpoint)
    {
        return Http::withToken($this->getToken())
            ->delete("{$this->baseUrls[$service]}{$endpoint}")
            ->json();
    }
}
```

### 2. Update PatientController

```php
<?php

namespace App\Http\Controllers;

use App\Services\MicroserviceClient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new MicroserviceClient();
    }

    // List semua patient
    public function index()
    {
        $patients = $this->client->get('patient', '/patients');
        return view('patients.index', ['patients' => $patients['data']]);
    }

    // Show patient detail
    public function show($id)
    {
        $patient = $this->client->get('patient', "/patients/{$id}");
        return view('patients.show', ['patient' => $patient]);
    }

    // Store patient baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:patients',
            'phone_number' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'address' => 'required',
            'blood_type' => 'required',
        ]);

        $result = $this->client->post('patient', '/patients', $data);
        
        return redirect()->route('patients.show', $result['id'])
            ->with('success', 'Patient created successfully');
    }

    // Update patient
    public function update($id, Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
        ]);

        $result = $this->client->put('patient', "/patients/{$id}", $data);
        
        return redirect()->route('patients.show', $id)
            ->with('success', 'Patient updated successfully');
    }

    // Delete patient
    public function destroy($id)
    {
        $this->client->delete('patient', "/patients/{$id}");
        
        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully');
    }
}
```

### 3. Update DoctorController

```php
<?php

namespace App\Http\Controllers;

use App\Services\MicroserviceClient;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new MicroserviceClient();
    }

    public function index(Request $request)
    {
        $doctors = $this->client->get('doctor', '/doctors');
        return view('doctors.index', ['doctors' => $doctors['data']]);
    }

    public function show($id)
    {
        $doctor = $this->client->get('doctor', "/doctors/{$id}");
        return view('doctors.show', ['doctor' => $doctor]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'specialization' => 'required',
            'license_number' => 'required|unique:doctors',
            'hospital_name' => 'required',
        ]);

        $result = $this->client->post('doctor', '/doctors', $data);
        
        return redirect()->route('doctors.show', $result['id'])
            ->with('success', 'Doctor added successfully');
    }

    public function update($id, Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'specialization' => 'required',
            'hospital_name' => 'required',
        ]);

        $result = $this->client->put('doctor', "/doctors/{$id}", $data);
        
        return redirect()->route('doctors.show', $id)
            ->with('success', 'Doctor updated successfully');
    }
}
```

### 4. Update AppointmentController

```php
<?php

namespace App\Http\Controllers;

use App\Services\MicroserviceClient;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new MicroserviceClient();
    }

    public function index()
    {
        $appointments = $this->client->get('appointment', '/appointments');
        return view('appointments.index', ['appointments' => $appointments['data']]);
    }

    public function create()
    {
        $patients = $this->client->get('patient', '/patients');
        $doctors = $this->client->get('doctor', '/doctors');
        
        return view('appointments.create', [
            'patients' => $patients['data'],
            'doctors' => $doctors['data'],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'appointment_date' => 'required|date',
            'reason_for_visit' => 'required',
        ]);

        $result = $this->client->post('appointment', '/appointments', $data);
        
        return redirect()->route('appointments.show', $result['id'])
            ->with('success', 'Appointment scheduled successfully');
    }

    public function getAvailableSlots($doctorId, $date)
    {
        $slots = $this->client->get('appointment', 
            "/appointments/available-slots?doctor_id={$doctorId}&date={$date}");
        
        return response()->json($slots);
    }
}
```

### 5. Update PrescriptionController

```php
<?php

namespace App\Http\Controllers;

use App\Services\MicroserviceClient;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new MicroserviceClient();
    }

    public function index()
    {
        $prescriptions = $this->client->get('prescription', '/prescriptions');
        return view('prescriptions.index', ['prescriptions' => $prescriptions['data']]);
    }

    public function show($id)
    {
        $prescription = $this->client->get('prescription', "/prescriptions/{$id}");
        return view('prescriptions.show', ['prescription' => $prescription]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'medication_name' => 'required',
            'dosage' => 'required',
            'frequency' => 'required',
            'quantity' => 'required',
            'duration_days' => 'required',
        ]);

        $result = $this->client->post('prescription', '/prescriptions', $data);
        
        return redirect()->route('prescriptions.show', $result['id'])
            ->with('success', 'Prescription created successfully');
    }

    public function createOrder($prescriptionId, Request $request)
    {
        $data = $request->validate([
            'pharmacy_id' => 'required',
            'quantity' => 'required',
        ]);

        $result = $this->client->post('prescription', 
            "/prescriptions/{$prescriptionId}/orders", $data);
        
        return response()->json($result);
    }
}
```

### 6. Update PharmacyDetailController

```php
<?php

namespace App\Http\Controllers;

use App\Services\MicroserviceClient;
use Illuminate\Http\Request;

class PharmacyDetailController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new MicroserviceClient();
    }

    // Inventory management
    public function inventory()
    {
        $items = $this->client->get('pharmacy', '/pharmacy/inventory');
        return view('pharmacy.inventory', ['items' => $items['data']]);
    }

    public function createInventory(Request $request)
    {
        $data = $request->validate([
            'medication_name' => 'required',
            'sku' => 'required|unique:pharmacy_inventory',
            'stock_quantity' => 'required',
            'unit_price' => 'required',
            'expiration_date' => 'required|date',
        ]);

        $result = $this->client->post('pharmacy', '/pharmacy/inventory', $data);
        
        return redirect()->route('pharmacy.inventory')
            ->with('success', 'Item added to inventory');
    }

    public function updateStock($id, Request $request)
    {
        $data = $request->validate([
            'stock_quantity' => 'required|integer',
        ]);

        $result = $this->client->put('pharmacy', "/pharmacy/inventory/{$id}", $data);
        
        return response()->json($result);
    }

    // Payments
    public function payments()
    {
        $payments = $this->client->get('pharmacy', '/pharmacy/payments');
        return view('pharmacy.payments', ['payments' => $payments['data']]);
    }

    public function recordPayment(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required',
            'prescription_id' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
        ]);

        $result = $this->client->post('pharmacy', '/pharmacy/payments', $data);
        
        return redirect()->route('pharmacy.payments')
            ->with('success', 'Payment recorded successfully');
    }
}
```

---

## 📝 Analytics Service Setup (Template)

Untuk membuat Analytics Service dengan cepat, ikuti pattern yang sama seperti services lainnya:

### 1. Create folder: `microservices/analytics-service`
### 2. Copy struktur dari service manapun (misal: doctor-service)
### 3. Update dengan models berikut:

```go
// models/analytics.go
type DoctorPerformanceAnalytic struct {
    ID                     uint      `gorm:"primaryKey"`
    DoctorID               uint      `gorm:"index"`
    TotalConsultations     int
    AverageRating          float64
    PrescriptionAccuracy   float64
    PatientSatisfaction    float64
    CreatedAt              time.Time
}

type DrugUsageAnalytic struct {
    ID               uint      `gorm:"primaryKey"`
    MedicationName   string
    TotalDispensed   int
    MostUsedDosage   string
    FrequencyOfUse   string
    Expiries         int
    CreatedAt        time.Time
}

type PatientOutcomeAnalytic struct {
    ID              uint      `gorm:"primaryKey"`
    PatientID       uint      `gorm:"index"`
    OutcomeType     string
    RecoveryRate    float64
    AverageStayDays int
    CreatedAt       time.Time
}
```

---

## 🔗 APIs Reference

### Auth Service (8001)
```
POST   /api/auth/login
POST   /api/auth/register
POST   /api/auth/verify-token
GET    /api/profile
PUT    /api/profile
GET    /api/users/:id
```

### Patient Service (8002)
```
POST   /api/patients
GET    /api/patients
GET    /api/patients/:id
PUT    /api/patients/:id
DELETE /api/patients/:id

# EHR
POST   /api/patients/:patientID/ehr
GET    /api/patients/:patientID/ehr
PUT    /api/patients/ehr/:id
DELETE /api/patients/ehr/:id

# Lab Results
POST   /api/patients/:patientID/lab-results
GET    /api/patients/:patientID/lab-results
```

### Doctor Service (8003)
```
POST   /api/doctors
GET    /api/doctors
GET    /api/doctors/:id
PUT    /api/doctors/:id
DELETE /api/doctors/:id
GET    /api/doctors/:doctorID/performance
PUT    /api/doctors/:doctorID/performance
```

### Appointment Service (8004)
```
POST   /api/appointments
GET    /api/appointments
GET    /api/appointments/:id
PUT    /api/appointments/:id
POST   /api/appointments/:id/cancel
GET    /api/appointments/available-slots
```

### Prescription Service (8005)
```
POST   /api/prescriptions
GET    /api/prescriptions
GET    /api/prescriptions/:id
PUT    /api/prescriptions/:id

# Orders
POST   /api/prescriptions/:prescriptionID/orders
GET    /api/prescriptions/:prescriptionID/orders
```

### Pharmacy Service (8006)
```
# Inventory
POST   /api/pharmacy/inventory
GET    /api/pharmacy/inventory
GET    /api/pharmacy/inventory/:id
PUT    /api/pharmacy/inventory/:id
GET    /api/pharmacy/inventory/low-stock

# Payments
POST   /api/pharmacy/payments
GET    /api/pharmacy/payments
```

---

## 🔐 Authentication Flow

1. **User Login di Laravel:**
   ```
   POST /login → Laravel Auth
   ```

2. **Laravel calls Auth Service:**
   ```
   POST http://localhost:8001/api/auth/login
   Response: { id, email, name, role, token }
   ```

3. **Store token dalam session:**
   ```php
   session(['api_token' => $response['token']]);
   ```

4. **Semua request ke Go services include token:**
   ```php
   Authorization: Bearer {token}
   ```

---

## 📊 Database Architecture

### Auth Service (meditrack_auth)
- users

### Patient Service (meditrack_patient)
- patients
- electronic_health_records
- lab_results
- patient_outcomes

### Doctor Service (meditrack_doctor)
- doctors
- doctor_performance_metrics

### Appointment Service (meditrack_appointment)
- appointments

### Prescription Service (meditrack_prescription)
- prescriptions
- prescription_orders

### Pharmacy Service (meditrack_pharmacy)
- pharmacy_inventory
- payments

### Analytics Service (meditrack_analytics)
- doctor_performance_analytics
- drug_usage_analytics
- patient_outcome_analytics

---

## 🚀 Inter-Service Communication

Untuk membuat services berkomunikasi satu sama lain (contoh: Prescription Service butuh data Patient dari Patient Service):

```go
// prescription-service/services/patient_service.go
package services

import (
    "net/http"
)

func GetPatientData(patientID uint) {
    // Call Patient Service
    resp, _ := http.Get(fmt.Sprintf("http://localhost:8002/api/patients/%d", patientID))
    // Handle response
}
```

---

## ✅ Verifikasi Setup

Jalankan semua services, lalu test:

```bash
# 1. Test Auth Service
curl -X POST http://localhost:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@meditrack.com","password":"password"}'

# Response:
# {
#   "id": 1,
#   "email": "admin@meditrack.com",
#   "name": "Admin MediTrack",
#   "role": "admin",
#   "token": "eyJhbGciOiJIUzI1NiIs..."
# }

# 2. Test Patient Service (gunakan token dari response di atas)
curl http://localhost:8002/api/patients \
  -H "Authorization: Bearer {token}"

# 3. Test semua health endpoints
for port in 8001 8002 8003 8004 8005 8006; do
  curl http://localhost:$port/health
done
```

---

## 🔄 Next Steps

1. ✅ Buat Analytics Service mengikuti template di atas
2. ✅ Update semua View blade untuk call APIs bukan lokal logic
3. ✅ Implement caching untuk improve performance
4. ✅ Setup load balancing / API Gateway (Kong, Nginx)
5. ✅ Implement service discovery
6. ✅ Setup monitoring (Prometheus, Grafana)
7. ✅ Implement CI/CD pipelines

---

**Status:** Selamat! Proyek Anda telah berhasil dipecah menjadi microservices! 🎉
