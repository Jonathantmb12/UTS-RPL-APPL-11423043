# 🎨 ARCHITECTURE VISUALIZATION
## MediTrack Hybrid Architecture Diagrams

---

## 1️⃣ HIGH-LEVEL SYSTEM ARCHITECTURE

### Complete System Overview

```
┌──────────────────────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER (User Interfaces)                             │
├──────────────────────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐      ┌──────────────┐      ┌──────────────┐                   │
│  │   Web App    │      │  Mobile App  │      │  Admin Portal│                   │
│  │  (Vue.js)    │      │ (React Native)       │   (React)    │                   │
│  └──────────────┘      └──────────────┘      └──────────────┘                   │
│           │                    │                      │                          │
└───────────┼────────────────────┼──────────────────────┼──────────────────────────┘
            │                    │                      │
            ▼                    ▼                      ▼
┌──────────────────────────────────────────────────────────────────────────────────┐
│                        API GATEWAY (Single Entry Point)                           │
├──────────────────────────────────────────────────────────────────────────────────┤
│  ┌─────────────────────────────────────────────────────────────────────────────┐ │
│  │  • Authentication & Authorization (JWT)                                     │ │
│  │  • Rate Limiting & Throttling                                              │ │
│  │  • Request/Response Logging & Monitoring                                   │ │
│  │  • SSL/TLS Termination                                                     │ │
│  │  • Load Balancing & Routing                                                │ │
│  └─────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────┘
            │
            ├─────────────────────────────────────────────────────────────┐
            │                                                               │
            ▼                                                               ▼
┌──────────────────────────────────────────────┐    ┌──────────────────────────────┐
│  CORE MONOLITH (Healthcare Domain)           │    │  MICROSERVICES               │
│                                              │    │  (Supporting Services)       │
├──────────────────────────────────────────────┤    ├──────────────────────────────┤
│  Bounded Contexts:                           │    │ 1. Payment Service           │
│  ├── Patient Management                      │    │ 2. Notification Service      │
│  ├── Doctor Management                       │    │ 3. Analytics Service         │
│  ├── Appointment Scheduling                  │    │ 4. Insurance Service         │
│  ├── Prescription Management                 │    │ 5. Lab Integration Service   │
│  ├── Pharmacy Management                     │    │                              │
│  └── Health Records & Lab Services           │    │ Each service:                │
│                                              │    │ • Own database               │
│  Single MySQL Database (ACID Transactions)   │    │ • Own API endpoints          │
│  Laravel Framework                           │    │ • Message queue connected    │
│                                              │    │ • Independent deployment     │
└──────────────────────────────────────────────┘    └──────────────────────────────┘
            │                                               │
            └───────────────────────┬───────────────────────┘
                                    │
                        ┌───────────▼────────────┐
                        │  Message Queue         │
                        │  (RabbitMQ/Kafka)      │
                        │                        │
                        │  Event-driven async    │
                        │  communication         │
                        │                        │
                        └────────────────────────┘
                                    │
                    ┌───────────────┼───────────────┐
                    ▼               ▼               ▼
            ┌─────────────┐  ┌─────────────┐  ┌─────────────┐
            │  Caching    │  │  Logging    │  │  Monitoring │
            │  (Redis)    │  │  (ELK)      │  │  (Datadog)  │
            └─────────────┘  └─────────────┘  └─────────────┘

External Integrations:
┌───────────────────────┬───────────────────────┬───────────────────────┐
│  Payment Gateway      │  Insurance Provider   │  Lab System           │
│  (Stripe, PayPal)     │  (API Integration)    │  (Automated Sync)     │
└───────────────────────┴───────────────────────┴───────────────────────┘
```

---

## 2️⃣ BOUNDED CONTEXTS & DOMAIN BOUNDARIES

### Core Monolith Structure (Domain-Driven Design)

```
╔════════════════════════════════════════════════════════════════════════════╗
║                      MediTrack Core Monolith                               ║
║                                                                             ║
║  ┌──────────────────────────────────────────────────────────────────────┐  ║
║  │                  PATIENT Domain (Bounded Context 1)                  │  ║
║  │  ┌─────────────────────────────────────────────────────────────────┐ │  ║
║  │  │ Entities: Patient, HealthRecord, VitalSigns, MedicalHistory   │ │  ║
║  │  │ Services: PatientService, HealthRecordService                 │ │  ║
║  │  │ Repositories: PatientRepo, EHRRepo                            │ │  ║
║  │  │ Events: PatientRegistered, ProfileUpdated, EHRModified       │ │  ║
║  │  └─────────────────────────────────────────────────────────────────┘ │  ║
║  │  Boundary: Only Patient module handles patient data registration    │  ║
║  └──────────────────────────────────────────────────────────────────────┘  ║
║                                                                             ║
║  ┌──────────────────────────────────────────────────────────────────────┐  ║
║  │                  DOCTOR Domain (Bounded Context 2)                   │  ║
║  │  ┌─────────────────────────────────────────────────────────────────┐ │  ║
║  │  │ Entities: Doctor, Credential, Specialization, Performance     │ │  ║
║  │  │ Services: DoctorService, AvailabilityService                  │ │  ║
║  │  │ Repositories: DoctorRepo, CredentialRepo                      │ │  ║
║  │  │ Events: DoctorRegistered, SpecializationAdded, OnLeave       │ │  ║
║  │  └─────────────────────────────────────────────────────────────────┘ │  ║
║  │  Boundary: Doctor module manages all doctor credentials & availability │  ║
║  └──────────────────────────────────────────────────────────────────────┘  ║
║                                                                             ║
║  ┌──────────────────────────────────────────────────────────────────────┐  ║
║  │            APPOINTMENT Domain (Bounded Context 3)                     │  ║
║  │  ┌─────────────────────────────────────────────────────────────────┐ │  ║
║  │  │ Entities: Appointment, AppointmentSlot, Status                 │ │  ║
║  │  │ Services: AppointmentService, ConflictDetection, Scheduling    │ │  ║
║  │  │ Repositories: AppointmentRepo, SlotRepo                        │ │  ║
║  │  │ Events: AppointmentBooked, Confirmed, Completed, Cancelled    │ │  ║
║  │  └─────────────────────────────────────────────────────────────────┘ │  ║
║  │  Boundary: Appointment owns scheduling logic, Doctor/Patient refs   │  ║
║  │  Dependency: Reads Doctor availability, Patient info               │  ║
║  └──────────────────────────────────────────────────────────────────────┘  ║
║                                                                             ║
║  ┌──────────────────────────────────────────────────────────────────────┐  ║
║  │         PRESCRIPTION Domain (Bounded Context 4)                       │  ║
║  │  ┌─────────────────────────────────────────────────────────────────┐ │  ║
║  │  │ Entities: Prescription, Medication, DrugInteraction            │ │  ║
║  │  │ Services: PrescriptionService, MedicationService              │ │  ║
║  │  │ Repositories: PrescriptionRepo, MedicationRepo                │ │  ║
║  │  │ Events: PrescriptionIssued, Fulfilled, Expired, Updated       │ │  ║
║  │  └─────────────────────────────────────────────────────────────────┘ │  ║
║  │  Boundary: Prescription owns drug data, Doctor issues, Patient uses   │  ║
║  │  Dependency: Links to Patient, Doctor, Medication                    │  ║
║  └──────────────────────────────────────────────────────────────────────┘  ║
║                                                                             ║
║  ┌──────────────────────────────────────────────────────────────────────┐  ║
║  │           PHARMACY Domain (Bounded Context 5)                         │  ║
║  │  ┌─────────────────────────────────────────────────────────────────┐ │  ║
║  │  │ Entities: PharmacyInventory, PrescriptionOrder, Supplier      │ │  ║
║  │  │ Services: InventoryService, OrderFulfillment, Reorder         │ │  ║
║  │  │ Repositories: InventoryRepo, OrderRepo, SupplierRepo          │ │  ║
║  │  │ Events: StockUpdated, LowStock, OrderCreated, Fulfilled       │ │  ║
║  │  └─────────────────────────────────────────────────────────────────┘ │  ║
║  │  Boundary: Pharmacy owns inventory, receives prescription orders     │  ║
║  │  Dependency: Consumes Prescription domain, notifies Notification svc  │  ║
║  └──────────────────────────────────────────────────────────────────────┘  ║
║                                                                             ║
║  ┌──────────────────────────────────────────────────────────────────────┐  ║
║  │     HEALTH RECORDS & LABS Domain (Bounded Context 6)                 │  ║
║  │  ┌─────────────────────────────────────────────────────────────────┐ │  ║
║  │  │ Entities: LabRequisition, LabResult, LabOrder, AbnormalFlag   │ │  ║
║  │  │ Services: LabService, HealthRecordIntegration                  │ │  ║
║  │  │ Repositories: LabRepo, ResultRepo                              │ │  ║
║  │  │ Events: TestRequested, SampleCollected, ResultReady, Analyzed │ │  ║
║  │  └─────────────────────────────────────────────────────────────────┘ │  ║
║  │  Boundary: Lab owns test data, integrates with external labs         │  ║
║  │  Dependency: Updates EHR, Notifies Doctor & Patient                  │  ║
║  └──────────────────────────────────────────────────────────────────────┘  ║
║                                                                             ║
╚════════════════════════════════════════════════════════════════════════════╝
```

---

## 3️⃣ MICROSERVICES DEPLOYMENT ARCHITECTURE

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                     MICROSERVICE ECOSYSTEM                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌────────────────────────────────────────────────────────────────────────┐ │
│  │  PAYMENT MICROSERVICE                                                  │ │
│  │  ┌──────────────────────────────────────────────────────────────────┐  │ │
│  │  │ API Endpoints:                                          │          │ │
│  │  │ • POST   /api/payments                                  │          │ │
│  │  │ • GET    /api/payments/{id}                             │          │ │
│  │  │ • POST   /api/refunds                                   │          │ │
│  │  │ • GET    /api/transactions                              │          │ │
│  │  │ • POST   /api/webhooks/stripe                           │          │ │
│  │  │                                                         │          │ │
│  │  │ Message Events:                                         │          │ │
│  │  │ Consumes: appointment.booked, prescription.ordered      │          │ │
│  │  │ Produces: payment.completed, payment.failed             │          │ │
│  │  └──────────────────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────────────────┘ │
│                                                                              │
│  ┌────────────────────────────────────────────────────────────────────────┐ │
│  │  NOTIFICATION MICROSERVICE                                              │ │
│  │  ┌──────────────────────────────────────────────────────────────────┐  │ │
│  │  │ API Endpoints:                                          │          │ │
│  │  │ • POST   /api/notifications/send                        │          │ │
│  │  │ • GET    /api/notifications/{id}                        │          │ │
│  │  │ • GET    /api/notifications/user/{userId}               │          │ │
│  │  │ • POST   /api/preferences/{userId}                      │          │ │
│  │  │                                                         │          │ │
│  │  │ Message Events:                                         │          │ │
│  │  │ Consumes: *.created, *.completed, *.alert, *.reminder   │          │ │
│  │  │ Produces: notification.queued, notification.sent         │          │ │
│  │  │                                                         │          │ │
│  │  │ Channels: Email, SMS, Push Notification                 │          │ │
│  │  └──────────────────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────────────────┘ │
│                                                                              │
│  ┌────────────────────────────────────────────────────────────────────────┐ │
│  │  ANALYTICS MICROSERVICE                                                │ │
│  │  ┌──────────────────────────────────────────────────────────────────┐  │ │
│  │  │ API Endpoints:                                          │          │ │
│  │  │ • GET    /api/dashboard/kpis                            │          │ │
│  │  │ • GET    /api/analytics/doctor/{id}                     │          │ │
│  │  │ • GET    /api/analytics/patient/{id}                    │          │ │
│  │  │ • GET    /api/reports/generate/{type}                   │          │ │
│  │  │ • GET    /api/trends/{metric}                           │          │ │
│  │  │                                                         │          │ │
│  │  │ Message Events:                                         │          │ │
│  │  │ Consumes: All domain events for aggregation             │          │ │
│  │  │ Produces: analytics.updated                             │          │ │
│  │  │                                                         │          │ │
│  │  │ Database: Time-series optimized (ClickHouse, Druid)     │          │ │
│  │  └──────────────────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────────────────┘ │
│                                                                              │
│  ┌────────────────────────────────────────────────────────────────────────┐ │
│  │  INSURANCE & CLAIMS MICROSERVICE                                        │ │
│  │  ┌──────────────────────────────────────────────────────────────────┐  │ │
│  │  │ API Endpoints:                                          │          │ │
│  │  │ • POST   /api/claims/submit                             │          │ │
│  │  │ • GET    /api/claims/{id}                               │          │ │
│  │  │ • GET    /api/coverage/verify                           │          │ │
│  │  │ • GET    /api/patient/{id}/insurance                    │          │ │
│  │  │ • POST   /api/claims/{id}/appeal                        │          │ │
│  │  │                                                         │          │ │
│  │  │ Message Events:                                         │          │ │
│  │  │ Consumes: prescription.ordered, appointment.completed    │          │ │
│  │  │ Produces: claim.submitted, claim.approved, claim.denied  │          │ │
│  │  │                                                         │          │ │
│  │  │ External API: Insurance provider integration            │          │ │
│  │  └──────────────────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────────────────┘ │
│                                                                              │
│  ┌────────────────────────────────────────────────────────────────────────┐ │
│  │  LAB INTEGRATION MICROSERVICE                                           │ │
│  │  ┌──────────────────────────────────────────────────────────────────┐  │ │
│  │  │ API Endpoints:                                          │          │ │
│  │  │ • GET    /api/labs/sync                                 │          │ │
│  │  │ • POST   /api/labs/order                                │          │ │
│  │  │ • GET    /api/labs/results/{id}                         │          │ │
│  │  │ • POST   /api/labs/webhook (from Lab system)            │          │ │
│  │  │                                                         │          │ │
│  │  │ Message Events:                                         │          │ │
│  │  │ Consumes: lab.requisition.created                       │          │ │
│  │  │ Produces: lab.result.ready                              │          │ │
│  │  │                                                         │          │ │
│  │  │ External API: Lab system (polling or webhooks)          │          │ │
│  │  └──────────────────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────────────────┘ │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 4️⃣ DATA FLOW DIAGRAM - APPOINTMENT BOOKING WORKFLOW

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                    APPOINTMENT BOOKING DATA FLOW                              │
└──────────────────────────────────────────────────────────────────────────────┘

Step 1: Patient Requests Appointment
┌─────────────┐
│   Patient   │
│   Frontend  │
└──────┬──────┘
       │ POST /api/appointments
       │ {patient_id, doctor_id, preferred_date}
       ▼
    ┌──────────────┐
    │ API Gateway  │
    │ • Validate   │
    │ • Auth check │
    └──────┬───────┘
           │
Step 2: Appointment Service Processing
           ▼
    ┌──────────────────────────────┐
    │ Appointment Service          │
    │ (Monolith)                   │
    │ ┌────────────────────────────┤
    │ │ 1. Check doctor availability│
    │ │ 2. Detect conflicts        │
    │ │ 3. Reserve slot            │
    │ │ 4. Create appointment      │
    │ └────────────────────────────┤
    └──────┬───────────────────────┘
           │
Step 3: Store in Database
           ▼
    ┌──────────────────────────────┐
    │  MySQL (Core Monolith DB)    │
    │  appointments table updated  │
    └──────┬───────────────────────┘
           │
Step 4: Emit Event to Message Queue
           ▼
    ┌──────────────────────────────┐
    │ Message Queue (RabbitMQ)     │
    │ Topic: appointments.booked   │
    │ Payload:                     │
    │  {appointment_id, patient_id,│
    │   doctor_id, scheduled_date} │
    └──────┬───────────────────────┘
           │
           ├─────────────────────────┬─────────────────────────┬──────────────┐
           │                         │                         │              │
Step 5a:   ▼                         ▼                         ▼              ▼
      Payment Service          Notification Service    Insurance Service  Analytics Service
      • Create invoice         • Send confirmation     • Check coverage    • Update metrics
      • Store transaction ID   • Send reminder          • Begin claim prep  • Track events
      • Emit: payment.ready    • Emit: notif.sent      • Emit: eligible    • Emit: analyzed
           │                          │                      │                   │
           └──────────────────────────┼──────────────────────┼───────────────────┘
                                      │
Step 6: Return to Patient
                                      ▼
                         ┌──────────────────────────┐
                         │  Appointment Confirmed   │
                         │  to Patient Frontend     │
                         │  {appointment_id,        │
                         │   confirmation_code,     │
                         │   reminder_time}         │
                         └──────────────────────────┘
```

---

## 5️⃣ SERVICE BOUNDARIES & DEPENDENCIES

```
┌─────────────────────────────────────────────────────────────────────────┐
│               SERVICE BOUNDARY MAP (Who calls whom)                      │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│                    CORE MONOLITH BOUNDARIES                             │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │                                                                   │  │
│  │  Patient ────→ Appointment ────→ Prescription ────→ Pharmacy    │  │
│  │    │                │                  │               │        │  │
│  │    └────→ HealthRecord          Doctor ←──────────────┘        │  │
│  │                     │                                            │  │
│  │                     └────────→ Lab Services                     │  │
│  │                                                                   │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                          │ (via messages)                               │
│        ┌─────────────────┼──────────────┬──────────────┬───────────┐   │
│        │                 │              │              │           │   │
│        ▼                 ▼              ▼              ▼           ▼   │
│  ┌──────────────┐ ┌──────────────┐ ┌────────────┐ ┌──────────┐ ┌───┐ │
│  │   Payment    │ │Notification  │ │  Insurance │ │Analytics │ │Lab│ │
│  │  Microservice│ │ Microservice │ │ Microservice Microservice  │ServiceEvent-driven, async│
│  │              │ │              │ │            │ │ Service  │ │   │ │
│  │ (Stripe API) │ │(Email, SMS)  │ │(Provider API (Time-series) │ Integration)
│  │              │ │              │ │            │ │Consumers   │ │   │ │
│  └──────────────┘ └──────────────┘ └────────────┘ └──────────┘ └───┘ │
│                                                                          │
│  ALL MICROSERVICES ARE:                                                 │
│  ✓ Loosely coupled (message-based)                                      │
│  ✓ Independently deployable                                             │
│  ✓ Horizontally scalable                                                │
│  ✓ Have own databases                                                   │
│  ✓ Own API endpoints                                                    │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 6️⃣ EXTERNAL SYSTEM INTEGRATION POINTS

```
┌──────────────────────────────────────────────────────────────────────────┐
│                  EXTERNAL INTEGRATIONS & BOUNDARIES                       │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                            │
│  MediTrack System                                                         │
│  ┌────────────────────────────────────────────────────────────────────┐  │
│  │                                                                    │  │
│  │  ┌──────────────────────────────┐                                 │  │
│  │  │ Payment Microservice         │                                 │  │
│  │  └──────────────┬───────────────┘                                 │  │
│  │                 │                                                 │  │
│  │                 │ integration                                     │  │
│  └─────────────────┼─────────────────────────────────────────────────┘  │
│                    │                                                      │
│                    ▼                                                      │
│          ┌─────────────────────┐                                         │
│          │ Payment Gateway     │                                         │
│          │ • Stripe            │ (EXTERNAL)                              │
│          │ • PayPal            │                                         │
│          │ • Bank Transfer     │                                         │
│          └─────────────────────┘                                         │
│                                                                            │
│                                                                            │
│  MediTrack System                                                         │
│  ┌────────────────────────────────────────────────────────────────────┐  │
│  │                                                                    │  │
│  │  ┌──────────────────────────────┐                                 │  │
│  │  │ Insurance Microservice       │                                 │  │
│  │  └──────────────┬───────────────┘                                 │  │
│  │                 │                                                 │  │
│  │                 │ REST API / SFTP                                 │  │
│  └─────────────────┼─────────────────────────────────────────────────┘  │
│                    │                                                      │
│                    ▼                                                      │
│          ┌─────────────────────┐                                         │
│          │ Insurance Provider  │                                         │
│          │ • Coverage Check    │ (EXTERNAL)                              │
│          │ • Claim Submit      │                                         │
│          │ • Approval Status   │                                         │
│          └─────────────────────┘                                         │
│                                                                            │
│                                                                            │
│  MediTrack System                                                         │
│  ┌────────────────────────────────────────────────────────────────────┐  │
│  │                                                                    │  │
│  │  ┌──────────────────────────────┐                                 │  │
│  │  │ Lab Integration Microservice │                                 │  │
│  │  └──────────────┬───────────────┘                                 │  │
│  │                 │                                                 │  │
│  │                 │ Polling / Webhooks                              │  │
│  └─────────────────┼─────────────────────────────────────────────────┘  │
│                    │                                                      │
│                    ▼                                                      │
│          ┌─────────────────────┐                                         │
│          │ Lab Management Sys  │                                         │
│          │ • Test Ordering     │ (EXTERNAL)                              │
│          │ • Result Retrieval  │                                         │
│          │ • Sample Tracking   │                                         │
│          └─────────────────────┘                                         │
│                                                                            │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## 7️⃣ DEPLOYMENT CONTAINERS & SERVICES

```
┌──────────────────────────────────────────────────────────────────────────┐
│                           DEPLOYMENT ARCHITECTURE                         │
│                            (Kubernetes Pods)                              │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                            │
│  NAMESPACE: meditrack-production                                          │
│                                                                            │
│  ┌──────────────────────────────────────────────────────────────────────┐│
│  │ Services (Deployment Replicas: 3)                                      ││
│  ├──────────────────────────────────────────────────────────────────────┤│
│  │ • api-gateway (Kong/Nginx)       [3 replicas]  Port 8080            ││
│  │ • core-monolith (Laravel)        [3 replicas]  Port 8001-8003       ││
│  │ • payment-service (Express)      [2 replicas]  Port 8004-8005       ││
│  │ • notification-service (Node)    [3 replicas]  Port 8006-8008       ││
│  │ • analytics-service (Python)     [2 replicas]  Port 8009-8010       ││
│  │ • insurance-service (Spring)     [2 replicas]  Port 8011-8012       ││
│  │ • lab-integration-service (Node) [1 replica]   Port 8013            ││
│  └──────────────────────────────────────────────────────────────────────┘│
│                                                                            │
│  ┌──────────────────────────────────────────────────────────────────────┐│
│  │ Persistent Storage (StatefulSets)                                      ││
│  ├──────────────────────────────────────────────────────────────────────┤│
│  │ • MySQL (Primary - core monolith) [1 pod]                           ││
│  │   - PVC: 100GB SSD                                                  ││
│  │   - Backup: Daily snapshots                                         ││
│  │                                                                      ││
│  │ • PostgreSQL (Payment Service)    [1 pod]                           ││
│  │   - PVC: 50GB SSD                                                   ││
│  │                                                                      ││
│  │ • PostgreSQL (Insurance Service)  [1 pod]                           ││
│  │   - PVC: 30GB SSD                                                   ││
│  │                                                                      ││
│  │ • MongoDB (Notification logs)     [1 pod]                           ││
│  │   - PVC: 20GB SSD                                                   ││
│  │                                                                      ││
│  │ • ClickHouse (Analytics DB)       [1 pod]                           ││
│  │   - PVC: 200GB SSD                                                  ││
│  └──────────────────────────────────────────────────────────────────────┘│
│                                                                            │
│  ┌──────────────────────────────────────────────────────────────────────┐│
│  │ Infrastructure Services (DaemonSets)                                   ││
│  ├──────────────────────────────────────────────────────────────────────┤│
│  │ • RabbitMQ Cluster              [3 nodes]                           ││
│  │ • Redis Cache                   [3 nodes - Sentinel]                ││
│  │ • Prometheus (Metrics)          [1 pod]                             ││
│  │ • Elasticsearch (Logging)       [3 nodes]                           ││
│  │ • Kibana (Log Dashboard)        [1 pod]                             ││
│  │ • Jaeger (Tracing)              [1 pod]                             ││
│  └──────────────────────────────────────────────────────────────────────┘│
│                                                                            │
│  ┌──────────────────────────────────────────────────────────────────────┐│
│  │ Load Balancing & Security                                             ││
│  ├──────────────────────────────────────────────────────────────────────┤│
│  │ • Ingress Controller (Nginx Ingress)                                ││
│  │ • TLS Termination (Let's Encrypt)                                   ││
│  │ • Service Mesh (optional: Istio for advanced routing)               ││
│  │ • Network Policies (Pod-to-pod security)                            ││
│  │ • RBAC (Role-Based Access Control)                                  ││
│  └──────────────────────────────────────────────────────────────────────┘│
│                                                                            │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## 📊 ARCHITECTURE PRINCIPLES SUMMARY

| Principle | Implementation | Benefit |
|-----------|----------------|---------|
| **Single Responsibility** | Each bounded context owns its domain | Easy to maintain, clear ownership |
| **Separation of Concerns** | Core healthcare separate from infrastructure | Healthcare logic stays stable |
| **Loose Coupling** | Message queue-based async communication | Services fail independently |
| **High Cohesion** | Related logic grouped in same context | Easy to understand and test |
| **Scalability** | Independent service scaling | Cost efficiency at scale |
| **Resilience** | Circuit breakers, retries, timeouts | Graceful degradation |
| **Maintainability** | Clear boundaries, DDD principles | Easier onboarding for new developers |
| **Extensibility** | Modular design, message events | Easy to add new services |

---

**Report Date:** March 26, 2026
**Visualization Status:** ✅ COMPLETE & COMPREHENSIVE
