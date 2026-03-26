# 🩺 Patient Login & Portal Guide

## ✅ PATIENT ROLE SUDAH TERSEDIA!

Sistem MediTrack sekarang memiliki **complete Patient Portal** dengan fitur:
- 👁️ Lihat Appointment Saya
- 💊 Lihat Resep Saya  
- 📋 Lihat Riwayat Kesehatan

---

## 🔐 Patient Demo Accounts

Anda dapat login dengan salah satu dari 8 akun pasien berikut:

| Email | Password | Role |
|-------|----------|------|
| patient@meditrack.local | password123 | Patient |
| jane@meditrack.local | password123 | Patient |
| bob@meditrack.local | password123 | Patient |
| alice@meditrack.local | password123 | Patient |
| charlie@meditrack.local | password123 | Patient |
| diana@meditrack.local | password123 | Patient |
| edward@meditrack.local | password123 | Patient |
| fiona@meditrack.local | password123 | Patient |

---

## 🚀 Cara Login sebagai Patient

### Step 1: Buka Login Page
```
http://127.0.0.1:8000/login
```

### Step 2: Masukkan Patient Credentials
```
Email: patient@meditrack.local
Password: password123
```

### Step 3: Klik "Sign In"
Anda akan diarahkan ke Dashboard

---

## 📊 Patient Dashboard Features

Setelah login sebagai patient, Anda akan melihat menu:

### 1. **Dashboard** 
   - Overview informasi pasien
   - Quick links ke appointment dan resep

### 2. **Appointment Saya** 
   - Lihat semua appointment yang dijadwalkan
   - Status appointment (scheduled, confirmed, completed, cancelled)
   - Informasi dokter yang menangani
   - Tanggal dan waktu appointment
   - Alasan kunjungan

### 3. **Resep Saya**
   - Lihat semua resep dari dokter
   - Nama obat dan dosis
   - Tanggal resep dan tanggal kadaluarsa
   - Status (aktif/kadaluarsa)
   - Informasi dokter yang meresepkan

### 4. **Riwayat Kesehatan**
   - Vital Signs (Tekanan Darah, Detak Jantung, Suhu)
   - Medical History
   - Current Medications
   - Allergies
   - Family History
   - Previous Surgeries
   - Lifestyle Notes

---

## 🔗 Patient Portal Routes

Setelah login sebagai patient, Anda bisa akses:

| Route | Description |
|-------|-------------|
| `/patient/appointments` | Lihat appointment saya |
| `/patient/prescriptions` | Lihat resep saya |
| `/patient/health-record` | Lihat riwayat kesehatan |

---

## 🎯 Testing Checklist untuk Patient

- [ ] Buka http://127.0.0.1:8000/login
- [ ] Login dengan `patient@meditrack.local` / `password123`
- [ ] Redirect ke Dashboard ✓
- [ ] Sidebar menampilkan menu Patient ✓
- [ ] Klik "Appointment Saya" - lihat appointments ✓
- [ ] Klik "Resep Saya" - lihat prescriptions ✓
- [ ] Klik "Riwayat Kesehatan" - lihat health records ✓
- [ ] Klik Logout - logout berhasil ✓
- [ ] Login dengan patient lain (jane@, bob@, dll) ✓

---

## 📝 Data yang Sudah Ada

Database sudah di-seed dengan data lengkap:

### Patient Data:
- 8 pasien dengan akun login
- Setiap pasien memiliki:
  - Electronic Health Record (EHR)
  - Multiple Appointments dengan dokter
  - Multiple Prescriptions
  - Vital signs dan medical history

### Doctor Data:
- 4 dokter dengan spesialisasi berbeda:
  - Dr. John Smith (Cardiology)
  - Dr. Sarah Johnson (Pediatrics)
  - Dr. Michael Brown (Orthopedics)
  - Dr. Emily Davis (Neurology)

### Appointment Data:
- 10 appointments sudah dijadwalkan
- Mix dari berbagai status (scheduled, confirmed, completed)

### Prescription Data:
- 15 prescriptions sudah dibuat
- Dengan berbagai jenis obat dan dosis

---

## 🔍 Troubleshooting

### Masalah: "404 Not Found" saat akses `/patient/appointments`
**Solusi:**
1. Pastikan sudah login sebagai patient (check navbar atas)
2. Clear cache: `php artisan cache:clear`
3. Pastikan middleware `role:patient` berfungsi

### Masalah: Menu Patient tidak muncul di sidebar
**Solusi:**
1. Logout dan login ulang
2. Check bahwa user role di database adalah "patient"
3. Clear browser cache (Ctrl+Shift+Delete)

### Masalah: Data appointment/resep kosong
**Solusi:**
1. Run seeder ulang: `php artisan db:seed`
2. Check database untuk pastikan data ada:
   ```bash
   php artisan tinker
   > \App\Models\Appointment::where('patient_id', 1)->count()
   ```

---

## 🔐 Security Notes

✅ Patient hanya bisa lihat data diri sendiri
✅ Tidak bisa akses data patient lain
✅ Route dilindungi middleware `role:patient`
✅ Session-based authentication
✅ CSRF protection enabled

---

## 📱 API Endpoints untuk Patient (Optional)

Jika ingin akses data via API:

```bash
# Get patient appointments
GET /api/patients/{id}/appointments

# Get patient prescriptions
GET /api/patients/{id}/prescriptions

# Get patient health record
GET /api/patients/{id}/health-record
```

Require authentication token.

---

## 🎉 Status

✅ Patient role **FULLY IMPLEMENTED**
✅ Patient portal **READY TO USE**
✅ Demo accounts **SEEDED**
✅ Routes **PROTECTED**
✅ Views **CREATED**

**Selamat testing! 🚀**

---

**Last Updated:** March 26, 2026
**Version:** 1.0.0
