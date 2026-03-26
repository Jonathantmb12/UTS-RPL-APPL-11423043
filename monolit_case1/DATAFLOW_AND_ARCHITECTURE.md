# MediTrack - Data Flow & System Architecture Diagram

## 🔄 Complete System Data Flow

### 1. PATIENT REGISTRATION & LOGIN FLOW

```
┌─────────────────────────────────────────────────────────────────────┐
│                   PATIENT REGISTRATION PROCESS                      │
└─────────────────────────────────────────────────────────────────────┘

Patient visits /register
    ↓
Submits form with:
  - name, email, password
  - date_of_birth, gender
  - phone_number, blood_type
  - allergies, address
    ↓
POST /api/auth/register
    ↓
AuthController::register()
    ├─ Validate all fields
    ├─ Check email uniqueness
    ├─ Hash password with bcrypt
    ├─ Create User record
    │   └─ role = 'patient'
    │   └─ is_active = true
    └─ Return JSON response
    ↓
Database: Insert into users table
    │   
    ↓ (Optional) Create Health Record
    │
ElectronicHealthRecord::create()
    └─ patient_id = $user->id
    └─ Initialize empty medical history
    ↓
Response: { user_id, email, token } → Patient (201 Created)
```

### 2. APPOINTMENT BOOKING FLOW

```
┌─────────────────────────────────────────────────────────────────────┐
│                   APPOINTMENT SCHEDULING FLOW                       │
└─────────────────────────────────────────────────────────────────────┘

Patient Portal: /patient/appointments
    ↓
Load Available Doctors:
  PatientController::myAppointments()
    └─ Query: User::where('role', 'doctor')->get()
    ↓
Fetch Doctor's Schedule:
  AppointmentController::getAvailableSlots($doctor_id)
    ├─ Get doctor's existing appointments
    ├─ Calculate 30-min slots (9AM-5PM)
    ├─ Mark occupied slots
    └─ Return available slots
    ↓
Patient Selects: Doctor + Date + Time
    ↓
POST /appointments
    │
    ├─ Validation Input:
    │   ├─ doctor_id (exists)
    │   ├─ appointment_date (after:now)
    │   ├─ reason_for_visit (required)
    │   ├─ consultation_type (in-person|video|phone)
    │   └─ duration_minutes (15-480, default 30)
    │
    ├─ AppointmentController::store()
    │   ├─ Conflict Detection:
    │   │   └─ Check if doctor has appointment in this slot
    │   │   └─ If YES → return error 409 Conflict
    │   │   └─ If NO → proceed
    │   │
    │   ├─ Create Appointment:
    │   │   ├─ patient_id = Auth::user()->id
    │   │   ├─ doctor_id = $request->doctor_id
    │   │   ├─ appointment_date = $request->date
    │   │   ├─ status = 'scheduled'
    │   │   └─ Insert into appointments table
    │   │
    │   ├─ Send Notification:
    │   │   ├─ Email to doctor (appointment scheduled)
    │   │   └─ Email to patient (confirmation)
    │   │
    │   └─ Response: { appointment_id, status, date } (201 Created)
    │
    ↓
Database: appointments table
    └─ Record: { patient_id, doctor_id, status='scheduled', date, ... }
    ↓
Status Transitions Available:
    scheduled → confirmed (Doctor confirms)
    → completed (After appointment)
    → cancelled (Either party can cancel)
    → rescheduled (Reschedule to another slot)
```

### 3. PRESCRIPTION ISSUANCE & PROCESSING FLOW

```
┌─────────────────────────────────────────────────────────────────────┐
│                    PRESCRIPTION WORKFLOW                            │
└─────────────────────────────────────────────────────────────────────┘

STEP 1: Doctor Issues Prescription
─────────────────────────────────────
Doctor views completed appointment
    ↓
Click "Issue Prescription"
    ↓
Form appears:
  - Medication name
  - Dosage (e.g., "500mg")
  - Frequency (e.g., "3x daily")
  - Quantity (number of pills/units)
  - Duration in days (e.g., "7 days")
  - Instructions for patient
  - Side effects warning
    ↓
POST /prescriptions
    ├─ Validation:
    │   ├─ medication_name (required, string)
    │   ├─ dosage (required, string)
    │   ├─ frequency (required, string)
    │   ├─ quantity (required, integer)
    │   ├─ duration_days (required, integer)
    │   └─ doctor_id = Auth::user()->id
    │
    ├─ PrescriptionDetailController::store()
    │   ├─ Create Prescription Record:
    │   │   ├─ patient_id = $request->patient_id
    │   │   ├─ doctor_id = Auth::user()->id
    │   │   ├─ appointment_id = $request->appointment_id
    │   │   ├─ medication_name = $request->medication
    │   │   ├─ dosage = $request->dosage
    │   │   ├─ frequency = $request->frequency
    │   │   ├─ quantity = $request->quantity
    │   │   ├─ duration_days = $request->duration_days
    │   │   ├─ prescribed_date = now()
    │   │   ├─ expiration_date = now()->addDays(duration_days)
    │   │   └─ status = 'active'
    │   │
    │   ├─ Insert into prescriptions table
    │   │
    │   ├─ Send Notifications:
    │   │   ├─ Email to patient (prescription issued)
    │   │   └─ Notify pharmacy system
    │   │
    │   └─ Response: { prescription_id, expiration_date } (201)
    │
    ↓
Database: prescriptions table
    └─ Record: { patient_id, doctor_id, medication_name, status='active', ... }

STEP 2: Patient/Staff Views & Orders Prescription
─────────────────────────────────────────────────
Patient Portal: /patient/prescriptions
    ├─ List all active & unexpired prescriptions
    ├─ Show: medication, dosage, frequency, expiration_date
    └─ Action: "Order from Pharmacy"
    ↓
Click "Order Prescription"
    ↓
Select Pharmacy
    ├─ Query: PharmacyInventory
    │   └─ where medication_name = $prescription->medication_name
    │   └─ where stock_quantity > 0
    ├─ Match doctor & prescription_id
    └─ Choose available pharmacy
    ↓
POST /prescription-orders
    ├─ Create PrescriptionOrder:
    │   ├─ prescription_id = $prescription->id
    │   ├─ pharmacy_id = selected_pharmacy_id
    │   ├─ patient_id = Auth::user()->id
    │   ├─ status = 'pending'
    │   ├─ total_price = pharmacy_inventory.unit_price * quantity
    │   ├─ ordered_date = now()
    │   └─ Insert into prescription_orders
    │
    ├─ Reduce Inventory:
    │   └─ PharmacyInventory::update()
    │   └─ stock_quantity -= quantity
    │
    ├─ Check Low Stock:
    │   ├─ IF stock_quantity <= reorder_level
    │   │   └─ Add to "Low Stock Alerts"
    │   │   └─ Notify pharmacy manager
    │   └─ ELSE continue
    │
    ├─ Create Payment Record:
    │   └─ Payment::create([
    │       'patient_id' => $patient->id,
    │       'payable_type' => 'PrescriptionOrder',
    │       'payable_id' => $prescriptionOrder->id,
    │       'amount' => $total_price,
    │       'status' => 'pending'
    │   ])
    │
    └─ Response: { order_id, total_price, status='pending' }
    ↓
Database Updates:
    ├─ prescription_orders: New order created
    ├─ pharmacy_inventory: stock_quantity reduced
    └─ payments: Payment record created

STEP 3: Pharmacy Processing & Fulfillment
──────────────────────────────────────────
Pharmacist Dashboard: /pharmacy/orders
    ├─ View: Pending prescription orders
    ├─ Display: medication name, quantity, patient, prescription details
    └─ Action buttons: "Confirm", "Cancel", "Ready"
    ↓
Pharmacist clicks "Confirm"
    ├─ PrescriptionOrder::update(status='confirmed')
    └─ Email notification to patient
    ↓
Pharmacist prepares medication
    ↓
Click "Ready for Pickup"
    ├─ PrescriptionOrder::update([
    │   'status' => 'ready',
    │   'ready_date' => now()
    │ ])
    ├─ Patient receives notification (SMS/Email)
    └─ Payment still pending or processing
    ↓
Patient picks up medication
    ↓
Pharmacist clicks "Picked Up"
    ├─ PrescriptionOrder::update([
    │   'status' => 'picked_up',
    │   'picked_up_date' => now()
    │ ])
    │
    ├─ Process Payment:
    │   ├─ Payment::update([
    │   │   'status' => 'completed',
    │   │   'paid_at' => now()
    │   │ ])
    │   │
    │   └─ Check Insurance Coverage:
    │       ├─ IF insurance available
    │       │   ├─ insurance_coverage = qualify_amount
    │       │   ├─ patient_payment = total - insurance
    │       │   └─ Create InsuranceClaim
    │       └─ ELSE
    │           └─ patient_payment = total_price
    │
    ├─ Send Receipt:
    │   └─ Email patient with payment summary
    │
    └─ Archive Order
        └─ Ready for next

Status Flow:
    pending → confirmed → ready → picked_up → closed
        ↓                             ↓
        cancelled                 refundable
```

### 4. ELECTRONIC HEALTH RECORDS FLOW

```
┌─────────────────────────────────────────────────────────────────────┐
│                  ELECTRONIC HEALTH RECORDS (EHR)                   │
└─────────────────────────────────────────────────────────────────────┘

CREATION: First appointment or patient registration
─────────────────────────────────────────────
PatientController::store()
    ├─ After creating patient User record
    │
    ├─ ElectronicHealthRecord::create([
    │   'patient_id' => $patient->id,
    │   'doctor_id' => null,  // Initially empty
    │   'medical_history' => '',
    │   'current_medications' => '',
    │   'allergies' => $patient->allergies,
    │   'blood_type' => $patient->blood_type,
    │   'height_cm' => null,
    │   'weight_kg' => null,
    │   'blood_pressure' => null,
    │   'other_vitals' => {} // JSON
    │ ])
    │
    └─ Empty record ready for doctor input

UPDATE: Doctor updates after appointment/examination
─────────────────────────
Doctor Portal: /appointments/{id}
    ├─ View appointment details & patient info
    ├─ See current EHR
    └─ Click "Update Health Record"
    ↓
Form appears with fields:
    ├─ Medical history (textarea)
    ├─ Current medications (textarea)
    ├─ Allergies (textarea)
    ├─ Previous surgeries (textarea)
    ├─ Family history (textarea)
    ├─ Vital signs:
    │   ├─ Height (cm)
    │   ├─ Weight (kg)
    │   ├─ Blood pressure (sys/dia)
    │   ├─ Heart rate
    │   └─ Temperature (°C)
    └─ Other vitals (JSON)
    ↓
PUT /electronic-health-records/{id}
    ├─ EHRController::update()
    │   ├─ doctor_id = Auth::user()->id
    │   ├─ Update all fields provided
    │   ├─ Store in DB
    │   └─ Log update timestamp
    │
    └─ Response: { success, updated_fields }
    ↓
Database: electronic_health_records
    └─ Updated record with latest vitals & history

VIEW: Patient/Doctor/Admin access
─────────────
GET /patient/health-record
    ├─ ElectronicHealthRecord::where('patient_id', Auth::user()->id)
    ├─ Return: All health data
    └─ Display in patient portal
    ↓
GET /doctors/{id}/health-records
    ├─ ElectronicHealthRecord::where('patient_id', $patient_id)
    ├─ Doctor views patient's complete medical history
    └─ Used during appointment for reference
```

### 5. PAYMENT & INSURANCE FLOW

```
┌─────────────────────────────────────────────────────────────────────┐
│              PAYMENT & INSURANCE CLAIMS PROCESSING                  │
└─────────────────────────────────────────────────────────────────────┘

PAYMENT CREATION: When order/service ready to bill
─────────────────────────────────────
Service completed (Prescription order picked up, appointment completed, etc.)
    ↓
Trigger Payment Creation:
    ├─ PrescriptionOrder picked up
    │   └─ PaymentController::createFromOrder($order)
    │
    └─ Appointment completed
        └─ PaymentController::createFromAppointment($appt)
    ↓
Payment::create([
    'patient_id' => $patient->id,
    'payable_type' => 'PrescriptionOrder',  // or 'Appointment'
    'payable_id' => $order->id,
    'transaction_id' => generateUnique(),
    'amount' => $total_amount,
    'payment_method' => 'credit_card',      // or other method
    'status' => 'pending',
    'paid_at' => null,
    'insurance_coverage' => null,
    'patient_payment' => null
])
    ↓
Insert into payments table

PAYMENT PROCESSING
──────────────────
Admin/System: View payments dashboard
    ↓
PaymentController::processPending()
    ├─ Get all payments with status='pending'
    ├─ Query insurance info for patient
    │
    ├─ For each payment:
    │   ├─ Check if patient has insurance
    │   │   └─ Query InsuranceClaim table
    │   │
    │   ├─ If insurance available:
    │   │   ├─ insurance_coverage = min(amount, policy_limit)
    │   │   ├─ patient_payment = amount - insurance_coverage
    │   │   ├─ Create InsuranceClaim:
    │   │   │   └─ InsuranceClaim::create([
    │   │   │       'patient_id' => $patient->id,
    │   │   │       'payment_id' => $payment->id,
    │   │   │       'insurance_provider' => 'Provider Name',
    │   │   │       'policy_number' => $policy,
    │   │   │       'claim_number' => generateClaimNo(),
    │   │   │       'status' => 'submitted',
    │   │   │       'claim_amount' => insurance_coverage,
    │   │   │       'submitted_date' => now()
    │   │   │   ])
    │   │   │
    │   │   └─ Email claim to insurance provider
    │   │
    │   └─ Else (no insurance):
    │       └─ patient_payment = amount
    │       └─ insurance_coverage = 0
    │
    ├─ Process Payment:
    │   ├─ Call payment gateway (Stripe, PayPal, etc.)
    │   ├─ If success:
    │   │   ├─ Payment::update([
    │   │   │   'status' => 'completed',
    │   │   │   'paid_at' => now(),
    │   │   │   'insurance_coverage' => $insurance_amt,
    │   │   │   'patient_payment' => $patient_amt,
    │   │   │   'payment_details' => gateway_response
    │   │   │ ])
    │   │   │
    │   │   └─ Send receipt to patient
    │   │
    │   └─ If failure:
    │       ├─ Payment::update(status='failed')
    │       └─ Notify patient
    │
    └─ Return: { processed, success, failed }

INSURANCE CLAIM TRACKING
────────────────────────
Admin Dashboard: /insurance-claims
    ├─ List all claims by status
    ├─ Status values: submitted, under_review, approved, rejected, appealed
    │
    ├─ Monitor:
    │   ├─ Submitted date
    │   ├─ Decision date
    │   ├─ Claim amount vs approved amount
    │   └─ Rejection reason (if any)
    │
    └─ Update claim status when insurance provider responds
        ├─ InsuranceClaim::update([
        │   'status' => 'approved',
        │   'approved_amount' => $amount,
        │   'decision_date' => now()
        │ ])
        │
        └─ Reconcile with payment
            └─ Adjust patient_payment if needed

PAYMENT STATUS LIFECYCLE
────────────────────────
pending
    ├─ Processing → completed
    │   └─ sent_at set, receipt generated
    │
    ├─ Failed → failed
    │   └─ Retry later or alternate method
    │
    └─ Cancelled → cancelled
        └─ Order cancelled before completion

Refund Flow:
completed
    ├─ Full refund → refunded
    │   ├─ refunded_at = now()
    │   └─ Amount returned to source
    │
    └─ Partial adjustments
        └─ Insurance rejects partial → patient_payment adjusted
```

### 6. LAB RESULTS WORKFLOW

```
┌─────────────────────────────────────────────────────────────────────┐
│                    LAB RESULTS WORKFLOW                             │
└─────────────────────────────────────────────────────────────────────┘

STEP 1: Doctor Orders Lab Test
────────────────────────────
Doctor during appointment or followup
    ├─ View patient
    ├─ Click "Order Lab Test"
    └─ Form appears:
        ├─ Test name (e.g., "Blood Test", "CT Scan")
        ├─ Test parameters (JSON format)
        │   └─ e.g., { "CBC", "Lipid Panel", "Glucose" }
        ├─ Description (optional)
        └─ Submit
    ↓
POST /lab-results
    ├─ LabResultController::store()
    │   ├─ Create LabResult:
    │   │   ├─ patient_id = $patient->id
    │   │   ├─ ordered_by_doctor_id = Auth::user()->id
    │   │   ├─ test_name = $request->test_name
    │   │   ├─ test_parameters = $request->parameters (JSON)
    │   │   ├─ status = 'ordered'
    │   │   ├─ ordered_date = now()
    │   │   └─ completed_date = null
    │   │
    │   ├─ Insert into lab_results
    │   │
    │   ├─ Send Order:
    │   │   ├─ Email to lab facility
    │   │   └─ Notify patient of appointment
    │   │
    │   └─ Response: { lab_id, status='ordered' }
    │
    ↓
Database: lab_results
    ├─ status = 'ordered'
    ├─ order_date recorded

STEP 2: Lab Performs Test
──────────────────────────
Lab facility processes order
    ├─ Receive order notification
    ├─ Update status:
    │   └─ LabResult::update(status='in-progress')
    │
    └─ Perform test procedures

STEP 3: Lab Records Results
────────────────────────────
Tests completed
    ├─ Record findings
    ├─ LabResult::update([
    │   'status' => 'completed',
    │   'results' => {               // JSON with actual results
    │       'CBC': { 'RBC': 4.5, 'WBC': 7.2, ... },
    │       'Glucose': 95,
    │       'Lipid': { 'Total': 180, 'HDL': 45, ... }
    │   },
    │   'clinical_notes' => 'All values within normal range',
    │   'completed_date' => now(),
    │   'test_file' => 'path/to/lab_report.pdf'
    │ ])
    │
    └─ Notify doctor & patient
        ├─ Email: Doctor new results available
        └─ Email: Patient results ready

STEP 4: Doctor & Patient View Results
───────────────────────────────────────
Doctor Portal:
    └─ GET /lab-results?patient_id=X
        ├─ View all tests ordered
        ├─ Filter by status (ordered, in-progress, completed)
        ├─ Click test to view results
        └─ Display: parameters, actual values, clinical_notes, file

Patient Portal:
    └─ GET /patient/lab-results
        ├─ View own lab results
        ├─ Doctor's clinical notes
        └─ Can download report PDF
            └─ Link to test_file (if available)
```

### 7. DOCTOR PERFORMANCE METRICS FLOW

```
┌─────────────────────────────────────────────────────────────────────┐
│              DOCTOR PERFORMANCE METRICS TRACKING                    │
└─────────────────────────────────────────────────────────────────────┘

AUTOMATIC CALCULATION: System updates metrics
─────────────────────
Every doctor has DoctorPerformanceMetric record (1:1)
    ├─ Created with doctor user account
    │
    └─ Updated when:
        ├─ Appointment status changes
        │   ├─ scheduled → increment total_appointments
        │   ├─ completed → increment completed_appointments
        │   └─ cancelled → increment cancelled_appointments
        │
        ├─ Patient rates doctor
        │   └─ Recalculate average_rating
        │
        └─ New patient assigned
            └─ Increment patient_count

Scheduled Job (Optional - Daily/Weekly):
    ├─ Scan all appointments
    ├─ Count metrics:
    │   ├─ total_appointments (all-time)
    │   ├─ completed_appointments (status='completed')
    │   ├─ cancelled_appointments (status='cancelled')
    │   │
    │   ├─ patient_count (distinct patients)
    │   │
    │   ├─ average_rating (from patient ratings, if exists)
    │   │
    │   ├─ response_time_hours (avg time from order to response)
    │   │
    │   └─ monthly_stats (JSON): {
    │       "2026-03": { appointments: 12, completed: 11, cancelled: 1 },
    │       "2026-02": { ... }
    │   }
    │
    └─ Update DoctorPerformanceMetric
        └─ last_updated = now()

Dashboard Display:
    ├─ Admin: /analytics
    │   └─ View all doctors' metrics
    │   └─ Sort by: rating, appointments, cancellation rate
    │
    └─ Doctor: /doctor/performance
        └─ View own metrics
        └─ Track personal KPIs
        └─ Identify improvement areas
```

## 📊 Complete Data Model Relationships Map

```
USER
├─ hasMany APPOINTMENT (doctor_id)
├─ hasMany APPOINTMENT (patient_id)
├─ hasMany PRESCRIPTION (doctor_id)
├─ hasMany PRESCRIPTION (patient_id)
├─ hasMany ELECTRONIC_HEALTH_RECORD (doctor_id)
├─ hasMany ELECTRONIC_HEALTH_RECORD (patient_id)
├─ hasMany LAB_RESULT (ordered_by_doctor_id)
├─ hasMany LAB_RESULT (patient_id)
├─ hasMany PHARMACY_INVENTORY (pharmacy_id)
├─ hasMany PRESCRIPTION_ORDER (pharmacy_id)
├─ hasMany PAYMENT (patient_id)
├─ hasMany INSURANCE_CLAIM (patient_id)
├─ hasMany PATIENT_OUTCOME (patient_id)
├─ hasMany PATIENT_OUTCOME (doctor_id)
├─ hasOne DOCTOR_PERFORMANCE_METRIC (doctor_id)
└─ hasMany ANALYTICS (morphMany)

APPOINTMENT
├─ belongsTo USER (patient_id)
├─ belongsTo USER (doctor_id)
└─ hasMany PRESCRIPTION

PRESCRIPTION
├─ belongsTo USER (patient_id)
├─ belongsTo USER (doctor_id)
├─ belongsTo APPOINTMENT
└─ hasMany PRESCRIPTION_ORDER

PRESCRIPTION_ORDER
├─ belongsTo PRESCRIPTION
├─ belongsTo USER (pharmacy_id)
├─ belongsTo USER (patient_id)
└─ morphOne PAYMENT

PAYMENT
├─ belongsTo USER (patient_id)
├─ morphTo payable (PRESCRIPTION_ORDER, APPOINTMENT, etc.)
└─ hasOne INSURANCE_CLAIM

INSURANCE_CLAIM
├─ belongsTo USER (patient_id)
└─ belongsTo PAYMENT

ELECTRONIC_HEALTH_RECORD
├─ belongsTo USER (patient_id)
└─ belongsTo USER (doctor_id)

LAB_RESULT
├─ belongsTo USER (patient_id)
└─ belongsTo USER (ordered_by_doctor_id)

PHARMACY_INVENTORY
└─ belongsTo USER (pharmacy_id)

DOCTOR_PERFORMANCE_METRIC
└─ belongsTo USER (doctor_id)

ANALYTICS
└─ morphTo entity (USER, DOCTOR, PRESCRIPTION, etc.)

PATIENT_OUTCOME
├─ belongsTo USER (patient_id)
└─ belongsTo USER (doctor_id, nullable)
```

## 🎯 API Request/Response Examples

### Appointment Creation
```
REQUEST:
POST /appointments
{
  "doctor_id": 2,
  "appointment_date": "2026-03-28 14:00:00",
  "duration_minutes": 30,
  "reason_for_visit": "Regular checkup",
  "consultation_type": "in-person"
}

RESPONSE SUCCESS (201):
{
  "success": true,
  "data": {
    "id": 42,
    "patient_id": 5,
    "doctor_id": 2,
    "appointment_date": "2026-03-28T14:00:00Z",
    "status": "scheduled",
    "duration_minutes": 30,
    "created_at": "2026-03-26T10:30:00Z"
  }
}

RESPONSE ERROR (409 Conflict):
{
  "success": false,
  "message": "Doctor has conflicting appointment",
  "error": "Doctor unavailable at this time"
}
```

### Prescription Creation
```
REQUEST:
POST /prescriptions
{
  "patient_id": 5,
  "medication_name": "Amoxicillin",
  "dosage": "500mg",
  "frequency": "3x daily",
  "quantity": 30,
  "duration_days": 7,
  "instructions": "Take with food",
  "side_effects_warning": "May cause stomach upset"
}

RESPONSE (201):
{
  "success": true,
  "data": {
    "id": 128,
    "medication_name": "Amoxicillin",
    "status": "active",
    "prescribed_date": "2026-03-26T10:30:00Z",
    "expiration_date": "2026-04-02T23:59:59Z",
    "doctor_id": 1
  }
}
```

---

**System Designed for:** Complete healthcare management with emphasis on data integrity, role-based access, and comprehensive audit trails through timestamps and soft deletes.
