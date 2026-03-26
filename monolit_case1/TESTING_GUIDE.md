# 🎯 MediTrack - Testing & Usage Guide

## ✅ SISTEM SUDAH LENGKAP - SIAP DITEST!

---

## 🌐 Akses Sistem

**URL:** `http://127.0.0.1:8000`

**Server Status:** Berjalan di port 8000
**Database:** MySQL di 127.0.0.1:3306 (database: applcase1)

---

## 🔐 Demo Akun yang Tersedia

### 1️⃣ ADMIN
```
Email: admin@meditrack.local
Password: password123
Role: admin
Permissions: Kelola semua data (pasien, dokter, appointment, resep)
```

### 2️⃣ DOCTOR
```
Email: dr.john@meditrack.local
Password: password123
(Alternatif: dr.sarah@, dr.michael@, dr.emily@meditrack.local)
Role: doctor
Permissions: Lihat/kelola appointment, buat resep, lihat pasien
```

### 3️⃣ PHARMACIST
```
Email: pharmacist@meditrack.local
Password: password123
Role: pharmacist
Permissions: Kelola inventory, lihat pesanan, alert stok rendah
```

### 4️⃣ PATIENT
```
8 pasien sudah di-seed di database
Pattern email: patient1@, patient2@, dst
Password: password123
Permissions: Lihat appointment, resep, riwayat kesehatan
```

---

## 🧪 Testing Workflow

### **TEST 1: Login sebagai Admin**

1. Buka http://127.0.0.1:8000/login
2. Masukkan:
   - Email: `admin@meditrack.local`
   - Password: `password123`
3. Klik **Sign In**
4. ✅ Seharusnya masuk ke Dashboard

#### Fitur Admin yang bisa dicoba:
- **Kelola Pasien** (sidebar)
  - [ ] Klik "Kelola Pasien"
  - [ ] Lihat daftar pasien (8 pasien sudah tersedia)
  - [ ] Klik "Add Patient" untuk tambah pasien baru
  - [ ] Klik nama pasien untuk lihat detail
  - [ ] Klik "Edit" untuk edit data
  - [ ] Klik "Delete" untuk hapus (dengan konfirmasi)

- **Kelola Dokter** (sidebar)
  - [ ] Klik "Kelola Dokter"
  - [ ] Lihat daftar dokter (4 dokter sudah tersedia)
  - [ ] Klik dokter untuk lihat detail & statistik
  - [ ] Klik "Add Doctor" untuk tambah dokter baru

- **Lihat Appointment** (sidebar)
  - [ ] Klik "Semua Appointment"
  - [ ] Lihat appointment yang sudah dibuat
  - [ ] Klik detail untuk lihat pasien & dokter

- **Lihat Prescription** (sidebar)
  - [ ] Klik link prescription jika ada
  - [ ] Lihat resep yang sudah dibuat

- **Logout**
  - [ ] Klik nama user di sidebar
  - [ ] Klik "Logout"
  - [ ] Seharusnya kembali ke login page

---

### **TEST 2: Login sebagai Doctor**

1. Logout dari admin
2. Login dengan:
   - Email: `dr.john@meditrack.local`
   - Password: `password123`
3. ✅ Seharusnya masuk ke Dashboard Doctor

#### Fitur Doctor yang bisa dicoba:
- **Dashboard** - Lihat statistik appointment & pasien
- **Appointment Saya** (sidebar) - Lihat appointment yang dijadwalkan
  - [ ] Klik appointment untuk detail
  - [ ] Lihat pasien, dokter, waktu
  - [ ] Lihat status (scheduled, confirmed, completed)

- **Pasien Saya** (sidebar) - Lihat pasien yang ditangani
  - [ ] Lihat daftar pasien
  - [ ] Klik pasien untuk detail
  - [ ] Lihat riwayat kesehatan

- **Resep Saya** (sidebar) - Lihat/kelola resep
  - [ ] Lihat resep yang dibuat
  - [ ] Klik "Add Prescription" untuk buat resep baru

---

### **TEST 3: Login sebagai Patient**

1. Logout dari doctor
2. Untuk test patient, perlu cari email pasien di database atau buat baru
3. Login dengan pasien credentials
4. ✅ Seharusnya masuk ke Dashboard Patient

#### Fitur Patient yang bisa dicoba:
- **Dashboard** - Overview kesehatan
- **Appointment Saya** (sidebar)
  - [ ] Lihat appointment yang dijadwalkan
  - [ ] Lihat dokter, tanggal, waktu
  - [ ] Lihat status

- **Resep Saya** (sidebar)
  - [ ] Lihat resep dari dokter
  - [ ] Lihat status (aktif/expired)
  - [ ] Lihat detail obat

- **Riwayat Kesehatan** (sidebar)
  - [ ] Lihat vital signs (BP, HR, Temp)
  - [ ] Lihat medical history
  - [ ] Lihat allergies
  - [ ] Lihat previous surgeries

---

### **TEST 4: Login sebagai Pharmacist**

1. Logout
2. Login dengan:
   - Email: `pharmacist@meditrack.local`
   - Password: `password123`
3. ✅ Seharusnya masuk ke Dashboard Pharmacist

#### Fitur Pharmacist yang bisa dicoba:
- **Inventory** (sidebar)
  - [ ] Lihat daftar obat (24 obat sudah tersedia)
  - [ ] Lihat stok, harga, expiry date
  - [ ] Klik "Add Medication" untuk tambah obat

- **Orders** (sidebar)
  - [ ] Lihat pesanan resep
  - [ ] Lihat status pesanan

- **Stok Rendah** (sidebar)
  - [ ] Lihat obat dengan stok rendah
  - [ ] Klik tombol untuk buat pesanan restock
  - [ ] Ada modal untuk input jumlah

---

## 🔍 Testing Checklist

### General
- [ ] Dapat akses login page
- [ ] Login berhasil dengan setiap role
- [ ] Dashboard tampil sesuai role
- [ ] Menu sidebar sesuai dengan role
- [ ] Logout berfungsi
- [ ] Redirect ke login ketika akses tanpa auth

### Patient Management (Admin)
- [ ] Lihat daftar pasien
- [ ] Search pasien berdasarkan nama
- [ ] Filter pasien berdasarkan blood type
- [ ] Klik pasien untuk detail
- [ ] Edit data pasien
- [ ] Hapus pasien
- [ ] Tambah pasien baru
- [ ] Lihat health record pasien

### Doctor Management (Admin)
- [ ] Lihat daftar dokter
- [ ] Klik dokter untuk detail
- [ ] Lihat specialization
- [ ] Lihat appointment count
- [ ] Lihat prescription count

### Appointment Management
- [ ] Lihat semua appointment
- [ ] Lihat pasien & dokter
- [ ] Lihat status appointment
- [ ] Lihat tanggal & waktu

### Prescription Management
- [ ] Lihat daftar resep
- [ ] Lihat obat, dosage, frequency
- [ ] Lihat status (aktif/expired)
- [ ] Lihat prescribed & expiry date

### Pharmacy Management (Pharmacist)
- [ ] Lihat inventory dengan stok
- [ ] Lihat low stock items
- [ ] Lihat pesanan
- [ ] Lihat expiry date obat

### Patient Portal (Patient)
- [ ] Lihat appointment saya
- [ ] Lihat resep saya
- [ ] Lihat health record
- [ ] Lihat vital signs
- [ ] Lihat medical history

---

## 📊 Data yang Sudah Tersedia

### Users
- 1 Admin
- 4 Doctors
- 3 Pharmacists
- 8 Patients
**Total: 16 users**

### Records
- 10 Appointments (dengan patient-doctor linkage)
- 15 Prescriptions (dengan patient-doctor-appointment linkage)
- 24 Pharmacy Inventory items (dengan stok, harga, expiry date)
- 8 Electronic Health Records (untuk setiap pasien)

---

## 🆘 Troubleshooting

### Login tidak berhasil?
```bash
# Reset database dengan seeder
php artisan migrate:fresh --seed
```

### Page tidak loading?
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Server tidak jalan?
```bash
# Pastikan server running di port 8000
php artisan serve --port=8000
```

### Fitur tidak tampil?
```bash
# Periksa middleware role di routes/web.php
# Pastikan Anda sudah login dengan role yang sesuai
```

---

## 📋 Fitur Lengkap yang Sudah Diimplementasikan

### ✅ Authentication
- Session-based login
- Password hashing
- Remember me
- Logout functionality
- Guest middleware

### ✅ Authorization
- Role-based middleware (admin, doctor, patient, pharmacist)
- Route protection
- View-level permission checks

### ✅ Patient Management
- CRUD operations (Create, Read, Update, Delete)
- Search & filter
- Relationship dengan appointments & prescriptions
- Electronic health record integration

### ✅ Doctor Management
- CRUD operations
- Specialization management (7 options)
- License verification
- Performance metrics

### ✅ Appointment System
- Scheduling dengan datetime picker
- Conflict detection
- Status workflow (scheduled → confirmed → completed)
- Doctor availability checking
- 8 slots per day, 30-min intervals

### ✅ Prescription System
- Medication database
- Dosage & frequency tracking
- Expiration date management
- Patient-doctor-appointment linkage
- Status tracking (active/expired)

### ✅ Pharmacy System
- Inventory management
- Stock tracking
- Low stock alerts
- Reorder functionality
- Expiry date monitoring

### ✅ Electronic Health Records
- Vital signs (BP, HR, Temperature)
- Medical history
- Current medications
- Allergies
- Previous surgeries
- Family history
- Lifestyle notes
- Last checkup date

### ✅ Dashboard & Analytics
- KPI cards (patients, doctors, appointments, prescriptions)
- Status overview
- Recent activities timeline
- Upcoming appointments preview
- Doctor performance metrics

### ✅ UI/UX
- Responsive Bootstrap 5.3 design
- Professional gradient colors
- Sidebar navigation
- Modal dialogs
- Form validation
- Alert messages
- Status badges
- Pagination

---

## 🎯 Next Steps

Setelah testing selesai, bisa lanjutkan dengan:

1. **Customization:**
   - Edit warna tema
   - Tambah logo
   - Customise field di form

2. **Enhancement:**
   - Tambah notifikasi email
   - SMS reminders untuk appointment
   - Export data ke PDF/Excel
   - Integrasi payment gateway

3. **Deployment:**
   - Setup di production server
   - Configure database production
   - Setup SSL certificate
   - Configure domain

---

## 📞 Support

**Jika ada masalah atau pertanyaan:**

1. Cek file documentation di root:
   - `COMPLETE_ROLE_GUIDE.md`
   - `COMPLETE_IMPLEMENTATION.md`
   - `SYSTEM_STATUS.md`
   - `QUICK_REFERENCE.md`

2. Cek Laravel logs:
   ```
   storage/logs/laravel.log
   ```

3. Test route dengan:
   ```bash
   php artisan route:list
   ```

---

**Sistem MediTrack siap digunakan! Selamat testing! 🎉**

Created: March 26, 2026
Status: ✅ Production Ready
Version: 1.0.0
