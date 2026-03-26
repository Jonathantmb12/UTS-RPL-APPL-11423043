# 🔧 SYSTEM DECOMPOSITION & MODELING
## MediTrack Module Architecture

---

## 📋 Overview

Berdasarkan Hybrid Architecture yang direkomendasikan, MediTrack diuraikan menjadi:
- **1 Core Monolith** dengan 6 bounded contexts
- **5 Strategic Microservices** untuk fungsi pendukung
- **1 API Gateway** untuk orchestration

---

## 1️⃣ CORE MONOLITH MODULES (Bounded Contexts)

### Module 1: **Patient Management** Domain
**Responsibilities:**
- Patient registration & profile management
- Patient health information tracking
- Patient appointment history
- Patient prescription history
- Patient consent & privacy management

**Key Entities:**
```
Patient (User sub-role)
  ├── profile data (name, DOB, contact)
  ├── medical_info (blood_type, allergies, emergency_contact)
  ├── health_status (current conditions)
  └── relationship to appointments, prescriptions

ElectronicHealthRecord
  ├── medical_history
  ├── current_medications
  ├── allergies
  ├── previous_surgeries
  ├── family_history
  ├── lifestyle_notes
  └── vital_signs tracking
```

**Responsibilities:**
```
Services:
✅ PatientService:
   - registerPatient()
   - updateProfile()
   - getHealthRecord()
   - trackVitalSigns()
   - managePrivacy()

✅ HealthRecordService:
   - createEHR()
   - updateMedicalHistory()
   - trackMedications()
   - recordAllergies()
   - auditTrail()
```

---

### Module 2: **Doctor Management** Domain
**Responsibilities:**
- Doctor registration & credentialing
- Doctor specialization management
- Doctor availability scheduling
- Doctor performance metrics
- Doctor leave/availability management

**Key Entities:**
```
Doctor (User sub-role)
  ├── credentials (license_number, specialization)
  ├── hospital affiliation
  ├── availability schedule
  ├── performance metrics
  └── relationships to appointments, prescriptions

DoctorPerformanceMetrics
  ├── patient_satisfaction_score
  ├── average_consultation_duration
  ├── prescription_efficiency
  ├── appointment_completion_rate
  └── training_records
```

**Responsibilities:**
```
Services:
✅ DoctorService:
   - registerDoctor()
   - updateCredentials()
   - manageAvailability()
   - scheduleLeave()
   - verifyLicense()

✅ PerformanceTrackingService:
   - calculateMetrics()
   - trackCompletions()
   - generateReports()
   - identifyTrainingNeeds()
```

---

### Module 3: **Appointment Management** Domain
**Responsibilities:**
- Appointment booking & scheduling
- Conflict detection
- Appointment confirmation & cancellation
- Appointment status tracking
- Appointment reminders

**Key Entities:**
```
Appointment
  ├── patient_id
  ├── doctor_id
  ├── appointment_date
  ├── duration_minutes (30, 60, etc.)
  ├── status (scheduled, confirmed, completed, cancelled)
  ├── reason_for_visit
  ├── consultation_type (in-person, virtual, phone)
  └── notes

AppointmentSlot
  ├── doctor_id
  ├── date
  ├── time_slots (segmented by 30 min)
  └── availability
```

**Responsibilities:**
```
Services:
✅ AppointmentService:
   - bookAppointment()
   - detectConflicts()
   - confirmAppointment()
   - cancelAppointment()
   - updateStatus()

✅ AvailabilityService:
   - checkAvailability()
   - reserveSlot()
   - releaseSlot()
   - generateSlots()

✅ AppointmentNotificationService:
   - sendReminders()  [→ Notification Service]
   - sendConfirmation [→ Notification Service]
   - sendReceipts [→ Notification Service]
```

---

### Module 4: **Prescription Management** Domain
**Responsibilities:**
- Prescription creation & issuance
- Medication information management
- Prescription tracking & expiration
- Prescription order fulfillment coordination
- Prescription history

**Key Entities:**
```
Prescription
  ├── patient_id
  ├── doctor_id
  ├── medication_name
  ├── dosage
  ├── frequency
  ├── duration
  ├── prescribed_date
  ├── expiry_date
  ├── quantity
  ├── status (active, expired, fulfilled)
  └── special_instructions

Medication
  ├── name
  ├── description
  ├── dosage_forms (tablet, liquid, injection)
  ├── side_effects
  ├── contraindications
  ├── pricing
  └── supplier_info
```

**Responsibilities:**
```
Services:
✅ PrescriptionService:
   - issuePrescription()
   - updatePrescription()
   - trackExpiration()
   - validateMedication()
   - generateHistory()

✅ MedicationService:
   - manageMedicationDatabase()
   - checkDrugInteractions()
   - trackSideEffects()
   - manageSuppliers()

✅ PrescriptionOrderService:
   - createOrder() [→ Pharmacy Domain]
   - trackFulfillment() [← Pharmacy Domain]
   - manageRefills()
```

---

### Module 5: **Pharmacy Management** Domain
**Responsibilities:**
- Inventory management
- Stock level tracking
- Low stock alerts
- Order fulfillment
- Reorder management

**Key Entities:**
```
PharmacyInventory
  ├── med_id
  ├── quantity_on_hand
  ├── reorder_level
  ├── reorder_quantity
  ├── expiry_date
  ├── location
  ├── unit_price
  └── supplier_info

PrescriptionOrder (linking Prescription → Pharmacy)
  ├── prescription_id
  ├── pharmacy_id
  ├── order_date
  ├── fulfillment_date
  ├── status
  └── cost
```

**Responsibilities:**
```
Services:
✅ InventoryService:
   - updateStock()
   - trackLevels()
   - alertLowStock()  [→ Notification Service]
   - manageExpiry()

✅ OrderFulfillmentService:
   - createOrder()
   - allocateStock()
   - generatePickList()
   - confirmFulfillment()
   - createShipment()

✅ ReorderService:
   - calculateReorderQuantity()
   - generatePurchaseOrders()
   - trackSupplierPerformance()
   - negotiatePrices()
```

---

### Module 6: **Health Records & Lab Services** Domain
**Responsibilities:**
- Lab test requisitions
- Lab result tracking
- Health record consolidation
- Medical history analysis
- Patient health analytics

**Key Entities:**
```
LabResult
  ├── patient_id
  ├── doctor_id
  ├── test_type
  ├── test_date
  ├── result_date
  ├── values
  ├── normal_range
  ├── abnormalities
  └── notes

LabRequisition
  ├── patient_id
  ├── doctor_id
  ├── tests_requested
  ├── priority
  ├── status
  └── collection_date
```

**Responsibilities:**
```
Services:
✅ LabService:
   - createRequisition()
   - trackSamples()
   - recordResults()
   - analyzeResults()
   - flagAbnormalities() [→ Notification Service]

✅ HealthRecordIntegrationService:
   - consolidateRecords()
   - analyzePatternTrends()
   - identifyRiskFactors()
   - generateSummaries()
```

---

## 2️⃣ MICROSERVICES (Extract from Monolith)

### Microservice 1: **Payment Service**
**Responsibilities:**
- Payment processing
- Multiple payment methods
- Payment tracking & audit
- Refund management

**Architecture:**
```
Technology: Express.js / FastAPI
Database: PostgreSQL (separate instance)
Queue: RabbitMQ (payments.queue)

Endpoints:
✅ POST /api/payments - Process payment
✅ GET /api/payments/{id} - Get payment status
✅ POST /api/refunds - Process refund
✅ GET /api/audit - Payment audit trail

Integration:
↔️ Message: appointment.paid (from monolith)
↔️ Message: prescription.ordered (from monolith)
→ Event: payment.completed (to monolith)
```

**Key Classes/Entities:**
```
Payment
  ├── transaction_id
  ├── amount
  ├── method (credit_card, bank, insurance)
  ├── status
  ├── timestamp
  └── related_to (appointment_id OR prescription_id)

PaymentMethod
  ├── type (Stripe, PayPal, bank transfer)
  ├── configuration
  └── status

Refund
  ├── payment_id
  ├── amount
  ├── reason
  ├── status
  └── timestamp
```

---

### Microservice 2: **Notification Service**
**Responsibilities:**
- Email notifications
- SMS notifications
- Push notifications
- Notification scheduling
- Delivery tracking

**Architecture:**
```
Technology: Node.js / Python
Database: MongoDB (notification logs)
Queue: RabbitMQ (notifications.queue)

Endpoints:
✅ POST /api/notifications - Send notification
✅ GET /api/notifications/{id} - Get status
✅ GET /api/logs - Get delivery logs

Integration:
← Events from:
  - Appointments (booking, reminder, completion)
  - Prescriptions (issued, ready, expired)
  - Payments (success, failed)
  - Labs (results ready)
  - System (alerts, maintenance)
```

**Key Classes/Entities:**
```
Notification
  ├── recipient_id
  ├── type (email, sms, push)
  ├── subject
  ├── body
  ├── status (pending, sent, failed)
  ├── scheduled_time
  ├── sent_time
  └── retry_count

NotificationTemplate
  ├── type (appointment_reminder, prescription_ready)
  ├── channels (email, sms, push)
  ├── template
  └── variables

DeliveryLog
  ├── notification_id
  ├── provider_id
  ├── status
  ├── timestamp
  └── error_message
```

---

### Microservice 3: **Analytics & Reporting Service**
**Responsibilities:**
- Health dashboards
- Doctor performance analytics
- Patient outcome tracking
- System usage analytics
- Report generation

**Architecture:**
```
Technology: Python / Node.js
Database: Apache Druid / ClickHouse (analytics DB)

Integration:
← Events from all modules (async/event-driven)
→ APIs for dashboard queries
→ PDF/Excel report generation
```

**Key Classes/Entities:**
```
AnalyticsEvent
  ├── event_type
  ├── entity_id
  ├── timestamp
  ├── metrics
  └── dimensions

DoctorAnalytics
  ├── doctor_id
  ├── appointments_completed
  ├── patient_satisfaction
  ├── avg_consultation_time
  ├── prescriptions_issued
  └── performance_score

PatientOutcomes
  ├── patient_id
  ├── appointment_count
  ├── appointment_completion_rate
  ├── health_improvement_score
  └── medication_adherence
```

---

### Microservice 4: **Insurance & Claims Service**
**Responsibilities:**
- Insurance verification
- Claims submission
- Coverage calculation
- Claims tracking
- Reimbursement processing

**Architecture:**
```
Technology: Spring Boot / Express.js
Database: PostgreSQL (separate)
Queue: RabbitMQ (insurance.queue)

Integration:
↔️ Insurance Provider APIs (async)
← Prescription orders (for claim submission)
← Appointments (for claim eligibility)
→ Coverage information (to monolith)
```

**Key Classes/Entities:**
```
InsuranceClaim
  ├── claim_id
  ├── patient_id
  ├── appointment_id OR prescription_id
  ├── amount
  ├── status (pending, approved, rejected)
  ├── submission_date
  ├── approval_date
  └── reimbursement_amount

PatientInsurance
  ├── patient_id
  ├── provider
  ├── policy_number
  ├── coverage_details
  ├── co_pay
  ├── deductible
  └── max_annual_benefit

CoverageVerification
  ├── patient_id
  ├── service_type
  ├── is_covered
  ├── coverage_percentage
  └── remaining_benefits
```

---

### Microservice 5: **Lab Integration Service**
**Responsibilities:**
- Lab system integration
- Test order management
- Result retrieval
- Data synchronization

**Architecture:**
```
Technology: Node.js / Python
Database: PostgreSQL
Queue: RabbitMQ (lab.queue)

Integration:
← Lab system APIs (polling/webhooks)
← Requisitions from monolith
→ Results to monolith
```

**Key Classes/Entities:**
```
LabOrder
  ├── order_id
  ├── patient_id
  ├── tests
  ├── priority
  ├── status
  ├── order_date
  └── collection_date

LabResult
  ├── result_id
  ├── order_id
  ├── test_type
  ├── values
  ├── normal_range
  ├── status
  └── result_date
```

---

## 3️⃣ CROSS-CUTTING CONCERNS

### API Gateway
```
Purpose: Route requests, authentication, rate limiting, logging

Features:
✅ Route to monolith or microservices
✅ JWT token validation
✅ Rate limiting per user
✅ Request/response logging
✅ SSL/TLS termination
✅ Load balancing

Technology: Kong / AWS API Gateway / Nginx
```

### Message Queue
```
Technology: RabbitMQ or Kafka

Topics/Queues:
- appointments.* (appointment events)
- prescriptions.* (prescription events)
- payments.* (payment events)
- notifications.* (notification requests)
- labs.* (lab events)
- insurance.* (insurance events)
- analytics.* (analytics events)

Pattern: Event-driven, async communication
```

### Database Synchronization
```
Pattern: Event Sourcing + CDC (Change Data Capture)

Use Cases:
✅ Payment completed → Update appointment.is_paid
✅ Lab results ready → Notify doctor & patient
✅ Prescription expired → Alert patient
✅ Stock low → Alert pharmacy
```

---

## 📊 MODULE RELATIONSHIPS MATRIX

```
┌─────────────────────────────────────────────────────────────────┐
│ Module Interactions (→ = calls/notifies, ↔ = bidirectional)     │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│ Patient Mgmt                  Doctor Mgmt                         │
│     ↓↑                            ↓↑                              │
│     └─→ Appointments ←────────────┘                              │
│         ↓↑          ↓↑                                            │
│         └─→ Prescriptions ←──────────┐                           │
│             ↓↑                       │                           │
│             └─→ Pharmacy Mgmt        │ (Payment Service)         │
│                 ↓↑                   │ (Notification Svc)        │
│                 └─→ PrescriptionOrder│ (Analytics Service)      │
│                     ↓                │ (Insurance Service)       │
│                     └────────────────┘                           │
│                                                                   │
│ Health Records & Lab ↔ All modules (reads EHR, appointments, etc) │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🎯 SUMMARY: MODULE RESPONSIBILITIES

| Module | Type | Primary Responsibility | Users |
|--------|------|---------------------|-------|
| **Patient Mgmt** | Monolith | Registration, profile, health info | Patients, Doctors, Admin |
| **Doctor Mgmt** | Monolith | Credentials, availability, metrics | Doctors, Admin |
| **Appointments** | Monolith | Scheduling, conflict detection | Patients, Doctors, Admin |
| **Prescriptions** | Monolith | Drug ordering, tracking | Doctors, Patients, Pharmacists |
| **Pharmacy** | Monolith | Inventory, fulfillment | Pharmacists, Admin |
| **Labs** | Monolith | Test requisition, results | Doctors, Patients, Labs |
| **Payment** | Microservice | Transaction processing | System |
| **Notifications** | Microservice | All communications | System |
| **Analytics** | Microservice | Reporting, dashboards | Admins, Doctors |
| **Insurance** | Microservice | Claims, coverage | System, Admin |
| **Lab Integration** | Microservice | External lab sync | System |

---

**Report Date:** March 26, 2026
**Decomposition Status:** ✅ COMPLETE & VALIDATED
