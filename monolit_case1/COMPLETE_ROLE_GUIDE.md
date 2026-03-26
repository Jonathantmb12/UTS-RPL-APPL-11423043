# 🏥 MediTrack - Sistem Peran & Fungsi Lengkap

## 👥 Peran (Roles) yang Tersedia

### 1. 👨‍💼 ADMIN
**Email:** `admin@meditrack.local`
**Password:** `password123`

#### Hak Akses & Fungsi:
- ✅ Dashboard dengan statistik lengkap
- ✅ **Kelola Pasien** (CRUD)
  - Lihat daftar pasien
  - Tambah pasien baru
  - Edit data pasien
  - Hapus pasien
  - Lihat riwayat kesehatan pasien
- ✅ **Kelola Dokter** (CRUD)
  - Lihat daftar dokter
  - Tambah dokter baru
  - Edit data dokter
  - Lihat spesialisasi dokter
- ✅ **Kelola Appointment** (CRUD)
  - Lihat semua appointment
  - Buat appointment
  - Konfirmasi appointment
  - Batalkan appointment
- ✅ **Kelola Prescription** (CRUD)
  - Lihat semua resep
  - Buat resep baru
  - Edit resep
- ✅ Logout

---

### 2. 👨‍⚕️ DOCTOR
**Email:** `dr.john@meditrack.local` (atau dokter lain)
**Password:** `password123`

#### Hak Akses & Fungsi:
- ✅ Dashboard personal
- ✅ **Appointment Saya**
  - Lihat appointment yang dijadwalkan
  - Konfirmasi appointment
  - Selesaikan appointment dengan catatan
- ✅ **Pasien Saya**
  - Lihat daftar pasien yang ditangani
  - Lihat detail pasien
  - Lihat riwayat kesehatan
- ✅ **Resep Saya**
  - Lihat resep yang dibuat
  - Buat resep baru
  - Edit resep
- ✅ Logout

---

### 3. 👤 PATIENT
**Pasien Demo** (bisa dibuat atau login dengan email pasien)
**Password:** `password123`

#### Hak Akses & Fungsi:
- ✅ Dashboard personal
- ✅ **Appointment Saya**
  - Lihat jadwal appointment saya
  - Lihat detail appointment
  - Lihat dokter yang menangani
- ✅ **Resep Saya**
  - Lihat resep yang diberikan
  - Lihat riwayat resep
  - Status resep (aktif/expired)
- ✅ **Riwayat Kesehatan**
  - Lihat catatan medis
  - Lihat hasil lab
  - Lihat vital signs
- ✅ Logout

---

### 4. 💊 PHARMACIST
**Email:** `pharmacist@meditrack.local`
**Password:** `password123`

#### Hak Akses & Fungsi:
- ✅ Dashboard personal
- ✅ **Inventory**
  - Lihat daftar obat
  - Lihat stok obat
  - Edit stok obat
  - Tambah obat baru
- ✅ **Pesanan (Orders)**
  - Lihat pesanan resep
  - Proses pesanan
  - Tandai sebagai selesai
- ✅ **Stok Rendah**
  - Lihat obat dengan stok rendah
  - Buat pesanan restock
- ✅ Logout

---

## 📋 Fitur Utama Sistem

### 1. Patient Management (Admin Only)
```
Route: /patients
Methods:
  - GET /patients → Lihat daftar pasien
  - GET /patients/create → Form tambah pasien
  - POST /patients → Simpan pasien baru
  - GET /patients/{id} → Detail pasien
  - GET /patients/{id}/edit → Edit form
  - PUT /patients/{id} → Update pasien
  - DELETE /patients/{id} → Hapus pasien
```

### 2. Doctor Management (Admin Only)
```
Route: /doctors
Methods:
  - GET /doctors → Lihat daftar dokter
  - GET /doctors/create → Form tambah dokter
  - POST /doctors → Simpan dokter baru
  - GET /doctors/{id} → Detail dokter
  - GET /doctors/{id}/edit → Edit form
  - PUT /doctors/{id} → Update dokter
  - DELETE /doctors/{id} → Hapus dokter
```

### 3. Appointment Management
```
Route: /appointments
Methods:
  - GET /appointments → Lihat semua appointment
  - GET /appointments/create → Form buat appointment
  - POST /appointments → Simpan appointment
  - GET /appointments/{id} → Detail appointment
  - PUT /appointments/{id} → Update appointment
  - DELETE /appointments/{id} → Batalkan appointment
  - POST /appointments/{id}/confirm → Konfirmasi
  - POST /appointments/{id}/cancel → Batalkan
  - POST /appointments/{id}/complete → Selesaikan
```

### 4. Prescription Management
```
Route: /prescriptions
Methods:
  - GET /prescriptions → Lihat resep
  - GET /prescriptions/create → Form buat resep
  - POST /prescriptions → Simpan resep
  - GET /prescriptions/{id} → Detail resep
  - PUT /prescriptions/{id} → Update resep
  - DELETE /prescriptions/{id} → Hapus resep
```

### 5. Pharmacy Management (Pharmacist Only)
```
Route: /pharmacy
Methods:
  - GET /pharmacy/inventory → Lihat inventory
  - GET /pharmacy/orders → Lihat pesanan
  - GET /pharmacy/low-stock → Lihat stok rendah
  - POST /pharmacy/add-medication → Tambah obat
```

### 6. Patient Portal (Patient Only)
```
Route: /patient
Methods:
  - GET /patient/appointments → Appointment saya
  - GET /patient/prescriptions → Resep saya
  - GET /patient/health-record → Riwayat kesehatan
```

---

## 🔐 Login & Testing

### Admin
```
Email: admin@meditrack.local
Password: password123
```

### Doctor
```
Email: dr.john@meditrack.local
Password: password123
(Atau: dr.sarah@meditrack.local, dr.michael@meditrack.local, dr.emily@meditrack.local)
```

### Pharmacist
```
Email: pharmacist@meditrack.local
Password: password123
```

### Patient
```
(8 pasien sudah di-seed, cari di database atau buat baru)
Pattern: patient1@meditrack.local, patient2@meditrack.local, dst
Password: password123
```

---

## 🗄️ Database Schema

### Users Table
- id, name, email, password, role (admin/doctor/patient/pharmacist)
- specialization (for doctors), license_number, hospital_name
- phone_number, address, date_of_birth, gender
- blood_type, allergies, is_verified, is_active, last_login_at
- created_at, updated_at

### Appointments Table
- id, patient_id, doctor_id, appointment_date, status
- consultation_type, reason, notes, created_at, updated_at

### Prescriptions Table
- id, patient_id, doctor_id, appointment_id
- medication_name, dosage, frequency, quantity
- prescribed_date, expiry_date, status

### PharmacyInventory Table
- id, medication_name, dosage, quantity, reorder_level
- price, expiry_date, supplier

### ElectronicHealthRecord Table
- id, patient_id, doctor_id, medical_history
- current_medications, allergies, last_checkup

---

## ✅ Checklist Fitur

| Feature | Admin | Doctor | Patient | Pharmacist |
|---------|-------|--------|---------|-----------|
| Dashboard | ✅ | ✅ | ✅ | ✅ |
| Kelola Pasien | ✅ | ❌ | ❌ | ❌ |
| Kelola Dokter | ✅ | ❌ | ❌ | ❌ |
| Appointment | ✅ | ✅ | ✅ | ❌ |
| Prescription | ✅ | ✅ | ✅ | ❌ |
| Pharmacy | ❌ | ❌ | ❌ | ✅ |
| Health Record | ❌ | ✅ | ✅ | ❌ |

---

## 🚀 Cara Testing

### 1. Login sebagai Admin
1. Buka http://127.0.0.1:8000/login
2. Email: `admin@meditrack.local`
3. Password: `password123`
4. Klik "Sign In"
5. Akan masuk ke Dashboard
6. Di sidebar, klik "Kelola Pasien" untuk test fitur pasien

### 2. Login sebagai Doctor
1. Logout dari admin
2. Login dengan `dr.john@meditrack.local`
3. Akan melihat menu Doctor
4. Bisa lihat appointment dan pasien

### 3. Login sebagai Pharmacist
1. Logout
2. Login dengan `pharmacist@meditrack.local`
3. Akan melihat menu Pharmacy
4. Bisa kelola inventory dan pesanan

### 4. Login sebagai Patient
1. Logout
2. Login dengan email pasien (akan ditampilkan di seeder)
3. Akan melihat appointment, resep, dan riwayat kesehatan

---

## 🎯 Kesimpulan

MediTrack adalah sistem manajemen kesehatan lengkap dengan:
- ✅ 4 roles dengan permission berbeda
- ✅ 50+ endpoints untuk berbagai fungsi
- ✅ 13 database tables untuk data lengkap
- ✅ 51 records demo data
- ✅ Responsive web interface
- ✅ Role-based access control
- ✅ Dashboard analytics

**Siap digunakan dan ditest!** 🎉
