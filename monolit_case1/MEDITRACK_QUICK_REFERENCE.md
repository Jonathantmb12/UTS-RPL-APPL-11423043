# MediTrack - Quick Reference Guide

## 🏗️ System Architecture at a Glance

```
┌─────────────────────────────────────────────────────────────────────┐
│                         MediTrack Healthcare System                 │
│                        Laravel 12 | MySQL | RBAC                   │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────┐    ┌─────────────────────┐    ┌──────────────┐
│  User Interface     │    │   Business Logic    │    │   Database   │
│     (Web/API)       │───▶│   (Controllers)     │───▶│   (MySQL)    │
└─────────────────────┘    └─────────────────────┘    └──────────────┘
         │                             │                      │
    - Blade Views              - 13 Controllers         - 14 Tables
    - API Endpoints            - Request Validation     - Relationships
    - Authentication           - Authorization          - Migrations
```

## 📦 Core Models & Their Relationships

```
USER (Central Hub)
├── PATIENT RELATIONSHIPS
│   ├─▶ Appointment (1:N as patient)
│   ├─▶ Prescription (1:N as patient)
│   ├─▶ PrescriptionOrder (1:N as patient)
│   ├─▶ ElectronicHealthRecord (1:1)
│   ├─▶ LabResult (1:N as patient)
│   ├─▶ Payment (1:N)
│   ├─▶ InsuranceClaim (1:N)
│   └─▶ PatientOutcome (1:N)
│
├── DOCTOR RELATIONSHIPS
│   ├─▶ Appointment (1:N as doctor)
│   ├─▶ Prescription (1:N as doctor)
│   ├─▶ LabResult (1:N as ordered_by_doctor)
│   ├─▶ ElectronicHealthRecord (1:N as doctor)
│   ├─▶ DoctorPerformanceMetric (1:1)
│   └─▶ PatientOutcome (1:N)
│
├── PHARMACIST RELATIONSHIPS
│   ├─▶ PharmacyInventory (1:N)
│   └─▶ PrescriptionOrder (1:N as pharmacy)
│
└── APPOINTMENT RELATIONSHIPS
    └─▶ Prescription (1:N)

PAYMENT (Polymorphic)
└─▶ PayableType can be: PrescriptionOrder, Appointment, etc.

ANALYTICS (Polymorphic)
└─▶ EntityType can be: Doctor, Patient, Medication, etc.
```

## 🎯 User Roles & Permissions Matrix

```
┌──────────┬─────────┬─────────┬────────────┬───────────┐
│ Resource │ Admin   │ Doctor  │ Pharmacist │ Patient   │
├──────────┼─────────┼─────────┼────────────┼───────────┤
│ Patients │ CRUD    │ View    │ -          │ Self Only │
│ Doctors  │ CRUD    │ Self    │ -          │ View      │
│ Appt.    │ CRUD    │ Own+    │ -          │ Own Only  │
│ Rx       │ CRUD    │ Issue   │ Process    │ View Own  │
│ Pharmacy │ View    │ -       │ CRUD       │ -         │
│ Payment  │ View    │ -       │ -          │ View Own  │
│ Analytics│ View    │ Own     │ Own        │ -         │
└──────────┴─────────┴─────────┴────────────┴───────────┘

Legend:
- CRUD = Full access (Create, Read, Update, Delete)
- View = Read-only access
- Own  = Can access only own records
- Own+ = Own records + assigned
- -    = No access
```

## 📊 Database Schema Overview

```
13 MODELS ─┐
           ├─ Data Models: User, Appointment, Prescription, etc.
           ├─ Analytics Models: DoctorPerformanceMetric, PatientOutcome
           └─ Support Models: Payment, InsuranceClaim, LabResult

14 TABLES ─┐
           ├─ users (Central table, polymorphic for roles)
           ├─ appointments (Patient-Doctor scheduling)
           ├─ prescriptions (Medication issuance)
           ├─ pharmacy_inventory (Medicine stock tracking)
           ├─ prescription_orders (Prescription fulfillment)
           ├─ payments (Transaction tracking - polymorphic)
           ├─ insurance_claims (Insurance processing)
           ├─ lab_results (Test ordering & results)
           ├─ electronic_health_records (Patient medical history)
           ├─ doctor_performance_metrics (Doctor KPIs)
           ├─ patient_outcomes (Treatment outcomes)
           ├─ drug_usage_analytics (Medication statistics)
           ├─ analytics (Generic analytics - polymorphic)
           └─ Cache & Jobs tables (Laravel infrastructure)

KEY FEATURES:
✓ Soft Deletes (SoftDeletes trait)
✓ Polymorphic Relationships (Payment, Analytics)
✓ Timestamps (created_at, updated_at)
✓ Indexes on foreign keys & search columns
✓ JSON fields for flexible data storage
```

## 🔄 Main Workflows

### Patient Books Appointment
```
Patient (Web)
    ↓
GET /patient/appointments
    ↓
View available doctors
    ↓
Select doctor & time
    ↓
POST /appointments
    ↓
AppointmentController::store()
    ├─ Validate input
    ├─ Check conflict (no double-booking)
    ├─ Create Appointment record
    └─ Return success/error
    ↓
Database: Insert into appointments table
```

### Doctor Issues Prescription
```
Doctor (Portal)
    ↓
View completed appointment
    ↓
Create prescription form
    ↓
POST /prescriptions
    ↓
PrescriptionDetailController::store()
    ├─ Validate medication details
    ├─ Calculate expiration_date
    ├─ Create Prescription record
    └─ Return success
    ↓
Database: Insert into prescriptions table
    ↓
Notification: Sent to patient & pharmacy
```

### Pharmacist Manages Inventory
```
Pharmacist (Web)
    ↓
GET /pharmacy/inventory
    ↓
View current stock
    ↓
Check low-stock items
    ↓
PUT /pharmacy/edit/{id}
    ├─ Update stock_quantity
    ├─ Check reorder_level
    └─ Create reorder if needed
    ↓
Database: Update pharmacy_inventory table
    ↓
Alert: Trigger if stock <= reorder_level
```

## 🚀 API Endpoints Summary

```
AUTHENTICATION (Public)
POST   /api/auth/register          Register new user
POST   /api/auth/login             Login and get token
GET    /api/health                 Health check

PATIENT MANAGEMENT (Admin + Self)
GET    /api/patients               List all patients
POST   /api/patients               Create patient
GET    /api/patients/{id}          Get patient details
PUT    /api/patients/{id}          Update patient
DELETE /api/patients/{id}          Delete patient

DOCTOR MANAGEMENT (Admin)
GET    /api/doctors                List all doctors
POST   /api/doctors                Create doctor
GET    /api/doctors/{id}           Get doctor details
PUT    /api/doctors/{id}           Update doctor
DELETE /api/doctors/{id}           Delete doctor

APPOINTMENT MANAGEMENT (Admin + Own)
GET    /api/appointments           List appointments
POST   /api/appointments           Create appointment
GET    /api/appointments/{id}      Get appointment
PUT    /api/appointments/{id}      Update appointment
DELETE /api/appointments/{id}      Cancel appointment

PRESCRIPTION MANAGEMENT (Admin + Doctor + Patient)
GET    /api/prescriptions          List prescriptions
POST   /api/prescriptions          Issue prescription
GET    /api/prescriptions/{id}     Get prescription
PUT    /api/prescriptions/{id}     Update prescription
DELETE /api/prescriptions/{id}     Delete prescription

PHARMACY MANAGEMENT (Pharmacist)
GET    /api/pharmacy/inventory     Get inventory
POST   /api/pharmacy/add-med       Add medication
GET    /api/pharmacy/orders        View orders
GET    /api/pharmacy/low-stock     Low-stock alerts

Total: 50+ endpoints
```

## 🎮 Controllers Overview

| Controller | Methods | Purpose |
|-----------|---------|---------|
| **AuthController** | register, login, logout | User authentication |
| **PatientController** | CRUD + relationships | Patient management |
| **DoctorController** | CRUD + statistics | Doctor management |
| **AppointmentController** | CRUD + confirm | Scheduling |
| **PrescriptionDetailController** | CRUD + filter | Prescription management |
| **PharmacyDetailController** | inventory, orders, low-stock | Pharmacy ops |
| **EHRController** | CRUD | Health records |
| **PaymentController** | CRUD | Payment processing |
| **AnalyticsController** | Dashboard data | System analytics |
| **LoginController** | Web login | Session auth |
| **PharmacyController** | Operations | Pharmacy management |
| **PrescriptionController** | Operations | Prescription ops |
| **Others** | Support functions | Utility controllers |

## 📈 Data Models Quick Reference

### User (13 fields visible, expandable)
```
id | name | email | password | role | 
phone_number | date_of_birth | gender | address |
blood_type | allergies | is_verified | is_active |
Plus role-specific: specialization, license_number, pharmacy_name, etc.
```

### Appointment (11 fields)
```
id | patient_id | doctor_id | appointment_date | status |
duration_minutes | reason_for_visit | consultation_type |
meeting_link | cancelled_at | cancellation_reason
```

### Prescription (13 fields)
```
id | patient_id | doctor_id | appointment_id |
medication_name | dosage | frequency | quantity |
duration_days | instructions | status |
prescribed_date | expiration_date
```

### PharmacyInventory (11 fields)
```
id | pharmacy_id | medication_name | generic_name | sku |
stock_quantity | reorder_level | reorder_quantity |
unit_price | expiration_date | batch_number
```

### Payment (13 fields - Polymorphic)
```
id | patient_id | payable_type | payable_id |
transaction_id | amount | payment_method | status |
payment_details (JSON) | insurance_coverage | patient_payment |
notes | paid_at | refunded_at
```

## 🔐 Authentication & Authorization

```
Login Flow:
1. User submits credentials (POST /login)
2. Laravel AuthenticatesUsers validates
3. Session created if valid
4. User redirected to dashboard
5. Routes protected by 'auth' middleware
6. Role-based routes protected by 'role:...' middleware

Role Validation:
EnsureUserRole Middleware
├─ Check if user is authenticated
├─ Check if user role matches route requirements
└─ Return 403 if unauthorized
```

## 📊 Entity Relationship Summary

```
Highest Relationship Density: USER model
├─ 8+ relationships as patient
├─ 6+ relationships as doctor
├─ 2+ relationships as pharmacist
└─ Polymorphic as payable in payments

Most Referenced: USER table
├─ Referenced by: appointments (2x), prescriptions (2x)
├─ Referenced by: electronic_health_records (2x), lab_results (2x)
├─ Referenced by: pharmacy_inventory, doctor_performance_metrics
└─ Referenced by: payment, insurance_claims, patient_outcome

Polymorphic Tables:
├─ payments (payable_type, payable_id)
├─ analytics (entity_type, entity_id)
└─ patient_outcomes (relates to doctor optionally)
```

## 🛠️ Testing Data

### Demo Accounts Available
```
ADMIN
└─ admin@meditrack.local : password123

DOCTORS (4)
├─ dr.john@meditrack.local : Cardiology
├─ dr.sarah@meditrack.local : Pediatrics
├─ dr.michael@meditrack.local : Orthopedics
└─ dr.emily@meditrack.local : Neurology

PHARMACISTS (3)
├─ pharmacist@meditrack.local : Central Pharmacy
├─ emma@meditrack.local : Downtown Pharmacy
└─ david@meditrack.local : Express Pharmacy

PATIENTS (8)
├─ patient@meditrack.local
├─ jane@meditrack.local
├─ bob@meditrack.local
├─ alice@meditrack.local
├─ charlie@meditrack.local
├─ diana@meditrack.local
├─ edward@meditrack.local
└─ fiona@meditrack.local

All passwords: password123
```

## 📈 System Scalability Notes

### Current Capacity
- **Pagination:** Default 10 records per page
- **Indexes:** On all foreign keys and status fields
- **Soft Deletes:** Preserve data without removing
- **Eager Loading:** Relationships loaded with queries

### Optimization Strategies
1. **Pagination** - Limit data per request
2. **Indexing** - Fast lookups on frequently searched columns
3. **Caching** - Can be added for dashboard analytics
4. **Query Optimization** - Eager loading to prevent N+1 queries
5. **Database Normalization** - Proper relationship design

### Future Enhancements
- [ ] API token-based authentication
- [ ] Real-time notifications (WebSocket)
- [ ] Advanced analytics dashboard
- [ ] Payment gateway integration
- [ ] SMS/Email notification service
- [ ] Lab system integration
- [ ] Insurance provider API
- [ ] Telemedicine features
- [ ] Mobile app (React Native/Flutter)
- [ ] Advanced reporting

## 📋 File Locations Quick Reference

```
Configuration:
├─ .env                          Environment variables
├─ config/app.php                Application config
├─ config/database.php           Database config
└─ config/auth.php               Authentication config

Code:
├─ app/Models/                   13 Eloquent models
├─ app/Http/Controllers/         13 Controllers
├─ app/Http/Middleware/          Route protection
├─ routes/web.php                Web routing
├─ routes/api.php                API routing
└─ routes/console.php            CLI commands

Database:
├─ database/migrations/           14 Migration files
├─ database/seeders/             Demo data seeding
└─ database/factories/           Test factories

Views & Frontend:
├─ resources/views/              Blade templates
├─ resources/css/                Stylesheets
├─ resources/js/                 JavaScript
└─ public/                        Static assets

Testing & Deployment:
├─ tests/                        Test files
├─ phpunit.xml                   Test config
├─ vite.config.js                Build config
└─ composer.json, package.json   Dependencies
```

## 🔥 Common Commands

```bash
# Setup
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed

# Running
php artisan serve              # Start server
npm run dev                    # Start Vite dev server

# Database
php artisan tinker             # Interactive shell
php artisan make:model ModelName -m  # Create model with migration
php artisan db:seed            # Run seeders

# Maintenance
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Testing
php artisan test
php artisan test --filter TestName

# Utilities
php artisan route:list         # Show all routes
php artisan make:controller ControllerName  # Create controller
```

---

## Summary Statistics

| Metric | Count |
|--------|-------|
| **Models** | 13 |
| **Controllers** | 13 |
| **Database Tables** | 14 |
| **Relationships** | 30+ |
| **API Endpoints** | 50+ |
| **User Roles** | 4 |
| **User Types** | 4 |
| **Demo User Accounts** | 16+ |
| **Demo Records** | 51+ |
| **Features Implemented** | 10+ modules |

**Overall Complexity: HIGH - Enterprise-Grade Healthcare System**
