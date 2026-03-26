# API Endpoints Quick Reference

## 🔐 Authentication Service (Port 8001)

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@meditrack.com",
  "password": "password"
}

Response: { id, name, email, role, token, created_at }
```

### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "role": "patient|doctor|pharmacist",
  "phone_number": "081234567890",
  "date_of_birth": "1990-01-01"
}

Response: { id, name, email, role, token }
```

### Get User Profile
```http
GET /api/profile
Authorization: Bearer {token}

Response: { id, name, email, role, phone_number, date_of_birth, is_active }
```

### Update Profile
```http
PUT /api/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Name",
  "phone_number": "081234567890",
  "date_of_birth": "1990-01-01"
}

Response: { id, name, email, role, phone_number }
```

### Get User by ID
```http
GET /api/users/{id}
Authorization: Bearer {token}

Response: { id, name, email, role, phone_number }
```

### Verify Token
```http
GET /api/verify
Authorization: Bearer {token}

Response: { valid: true, id, email, role }
```

---

## 👥 Patient Service (Port 8002)

### Get All Patients
```http
GET /api/patients?page=1&page_size=10
Authorization: Bearer {token}

Response: { data: [...], total, page, page_size }
```

### Get One Patient
```http
GET /api/patients/{id}
Authorization: Bearer {token}

Response: { id, user_id, name, email, phone_number, date_of_birth, blood_type, address, allergies }
```

### Create Patient
```http
POST /api/patients
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 2,
  "name": "Ahmad Wijaya",
  "email": "ahmad@example.com",
  "phone_number": "081234567890",
  "date_of_birth": "1990-01-01",
  "gender": "male",
  "address": "Jakarta",
  "emergency_contact": "081987654321",
  "blood_type": "O",
  "allergies": "Penicillin"
}

Response: { id, user_id, name, email, created_at }
```

### Update Patient
```http
PUT /api/patients/{id}
Authorization: Bearer {token}

{
  "name": "Updated Name",
  "address": "Bandung"
}

Response: { id, name, address, updated_at }
```

### Delete Patient
```http
DELETE /api/patients/{id}
Authorization: Bearer {token}

Response: { message: "Patient deleted successfully" }
```

### Search Patient
```http
GET /api/patients/search?q=ahmad
Authorization: Bearer {token}

Response: [{ id, name, email, phone_number }, ...]
```

### Electronic Health Records (EHR)

#### Get Patient EHRs
```http
GET /api/patients/{patient_id}/ehr
Authorization: Bearer {token}

Response: [{ id, patient_id, diagnosis, notes, created_at }, ...]
```

#### Create EHR
```http
POST /api/patients/{patient_id}/ehr
Authorization: Bearer {token}

{
  "diagnosis": "Hypertension",
  "treatment": "Antihypertensive medication",
  "notes": "Follow-up in 2 weeks"
}

Response: { id, patient_id, diagnosis, treatment, created_at }
```

### Lab Results

#### Get Patient Lab Results
```http
GET /api/patients/{patient_id}/lab-results
Authorization: Bearer {token}

Response: [{ id, patient_id, test_name, result, status, created_at }, ...]
```

#### Create Lab Result
```http
POST /api/patients/{patient_id}/lab-results
Authorization: Bearer {token}

{
  "test_name": "Blood Count",
  "test_type": "Hematology",
  "result": "Normal",
  "status": "pending"
}

Response: { id, patient_id, test_name, status, created_at }
```

#### Complete Lab Result
```http
PUT /api/lab-results/{id}/complete
Authorization: Bearer {token}

{
  "result": "Elevated RBC count",
  "interpretation": "Possible dehydration"
}

Response: { id, result, interpretation, status: "completed" }
```

---

## 👨⚕️ Doctor Service (Port 8003)

### Get All Doctors
```http
GET /api/doctors?page=1&specialization=Cardiology
Authorization: Bearer {token}

Response: { data: [...], total, page }
```

### Get One Doctor
```http
GET /api/doctors/{id}
Authorization: Bearer {token}

Response: { id, user_id, name, specialization, license_number, experience_years, rating }
```

### Create Doctor
```http
POST /api/doctors
Authorization: Bearer {token}

{
  "user_id": 3,
  "name": "Dr. Ahmad Wijaya",
  "specialization": "Cardiology",
  "license_number": "LIC123456",
  "experience_years": 10,
  "consultation_fee": 150000,
  "bio": "Specializing in heart conditions"
}

Response: { id, user_id, name, specialization, created_at }
```

### Get Doctor Performance
```http
GET /api/doctors/{id}/performance
Authorization: Bearer {token}

Response: { doctor_id, total_consultations, average_rating, patient_satisfaction_score }
```

### Update Performance Metric
```http
PUT /api/doctors/{id}/performance
Authorization: Bearer {token}

{
  "total_consultations": 150,
  "average_rating": 4.8,
  "patient_satisfaction_score": 4.7
}

Response: { doctor_id, total_consultations, average_rating }
```

---

## 📅 Appointment Service (Port 8004)

### Get All Appointments
```http
GET /api/appointments?page=1&patient_id=2
Authorization: Bearer {token}

Response: { data: [...], total, page }
```

### Get One Appointment
```http
GET /api/appointments/{id}
Authorization: Bearer {token}

Response: { id, patient_id, doctor_id, appointment_date, status, reason_for_visit }
```

### Create Appointment
```http
POST /api/appointments
Authorization: Bearer {token}

{
  "patient_id": 2,
  "doctor_id": 3,
  "appointment_date": "2024-03-26 14:00:00",
  "reason_for_visit": "Chest pain",
  "notes": "Follow-up from previous consultation"
}

Response: { id, patient_id, doctor_id, appointment_date, status: "scheduled" }
```

### Update Appointment
```http
PUT /api/appointments/{id}
Authorization: Bearer {token}

{
  "appointment_date": "2024-03-27 10:00:00",
  "reason_for_visit": "Updated reason"
}

Response: { id, appointment_date, updated_at }
```

### Cancel Appointment
```http
PATCH /api/appointments/{id}/cancel
Authorization: Bearer {token}

{
  "cancellation_reason": "Patient request"
}

Response: { id, status: "cancelled", cancelled_at }
```

### Get Available Slots
```http
GET /api/doctors/{doctor_id}/available-slots?date=2024-03-26
Authorization: Bearer {token}

Response: { doctor_id, date, slots: ["09:00", "09:30", "10:00", ...] }
```

---

## 💊 Prescription Service (Port 8005)

### Get All Prescriptions
```http
GET /api/prescriptions?page=1&patient_id=2
Authorization: Bearer {token}

Response: { data: [...], total, page }
```

### Get One Prescription
```http
GET /api/prescriptions/{id}
Authorization: Bearer {token}

Response: { id, patient_id, doctor_id, medication_name, dosage, frequency, quantity, duration_days, status, prescribed_date, expiration_date }
```

### Create Prescription
```http
POST /api/prescriptions
Authorization: Bearer {token}

{
  "patient_id": 2,
  "doctor_id": 3,
  "medication_name": "Lisinopril",
  "dosage": "10mg",
  "frequency": "Once daily",
  "quantity": 30,
  "duration_days": 30,
  "warnings": "Take with food",
  "notes": "Monitor blood pressure"
}

Response: { id, patient_id, medication_name, status: "active", expiration_date: "2024-04-26" }
```

### Update Prescription
```http
PUT /api/prescriptions/{id}
Authorization: Bearer {token}

{
  "dosage": "20mg",
  "quantity": 60
}

Response: { id, dosage, quantity, updated_at }
```

### Create Prescription Order
```http
POST /api/prescription-orders
Authorization: Bearer {token}

{
  "prescription_id": 5,
  "pharmacy_id": 1,
  "quantity_ordered": 30,
  "order_date": "2024-03-25"
}

Response: { id, prescription_id, pharmacy_id, status: "pending", created_at }
```

### Get Prescription Orders
```http
GET /api/prescription-orders?prescription_id=5
Authorization: Bearer {token}

Response: { data: [...], total }
```

---

## 🏥 Pharmacy Service (Port 8006)

### Get All Inventory
```http
GET /api/inventory?page=1&page_size=20
Authorization: Bearer {token}

Response: { data: [...], total, page }
```

### Get One Inventory Item
```http
GET /api/inventory/{id}
Authorization: Bearer {token}

Response: { id, medication_name, sku, stock_quantity, unit_price, reorder_level, batch_number, expiration_date }
```

### Create Inventory
```http
POST /api/inventory
Authorization: Bearer {token}

{
  "medication_name": "Lisinopril 10mg",
  "sku": "LISN-10-001",
  "stock_quantity": 100,
  "unit_price": 2.50,
  "reorder_level": 20,
  "reorder_quantity": 50,
  "batch_number": "BATCH2024001",
  "expiration_date": "2025-12-31"
}

Response: { id, medication_name, stock_quantity, created_at }
```

### Update Inventory
```http
PUT /api/inventory/{id}
Authorization: Bearer {token}

{
  "stock_quantity": 150,
  "unit_price": 2.75
}

Response: { id, stock_quantity, unit_price, updated_at }
```

### Get Low Stock Items
```http
GET /api/inventory/low-stock
Authorization: Bearer {token}

Response: [{ id, medication_name, stock_quantity, reorder_level }, ...]
```

### Create Payment
```http
POST /api/payments
Authorization: Bearer {token}

{
  "patient_id": 2,
  "prescription_id": 5,
  "amount": 75.00,
  "payment_method": "card",
  "notes": "Payment for Lisinopril"
}

Response: { id, patient_id, amount, status: "completed", receipt_number: "RCP20240325001", payment_date }
```

### Get Payments
```http
GET /api/payments?patient_id=2&page=1
Authorization: Bearer {token}

Response: { data: [...], total, page }
```

---

## 🔑 Common Response Format

All services follow this response format:

### Success (200, 201)
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  ...
  "created_at": "2024-03-25T10:30:00Z"
}
```

### Error (400, 401, 404, 500)
```json
{
  "error": "Error message",
  "message": "Detailed error explanation",
  "code": "ERROR_CODE"
}
```

### List Response
```json
{
  "data": [
    { "id": 1, "name": "Item 1" },
    { "id": 2, "name": "Item 2" }
  ],
  "total": 2,
  "page": 1,
  "page_size": 10
}
```

---

## 🧪 Testing with cURL

### Login
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@meditrack.com","password":"password"}'
```

### Get Patients
```bash
curl -X GET http://localhost:8002/api/patients \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Appointment
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

---

## 📱 postman Collection

Import this Postman environment:

```json
{
  "name": "MediTrack Microservices",
  "values": [
    {
      "key": "base_url_auth",
      "value": "http://localhost:8001/api",
      "type": "string"
    },
    {
      "key": "base_url_patient",
      "value": "http://localhost:8002/api",
      "type": "string"
    },
    {
      "key": "token",
      "value": "YOUR_TOKEN_HERE",
      "type": "string"
    }
  ]
}
```

Use `{{token}}` in Authorization header of all requests.

---

**Last Updated:** March 2024
