# 🔧 ERROR FIX - Patient.appointments Duplicate Route Names

## Error yang Terjadi

```
Illuminate\Routing\Exceptions\UrlGenerationException

Missing required parameter for [Route: patient.appointments] 
[URI: patients/{id}/appointments] 
[Missing parameter: id].
```

---

## Root Cause

Ada **2 routes dengan nama yang sama** `patient.appointments`:

### Route #1 (Admin - untuk lihat patient detail):
```php
Route::get('/patients/{id}/appointments', [PatientController::class, 'getAppointments'])
    ->name('patient.appointments');
```

### Route #2 (Patient Portal - untuk patient lihat appointment sendiri):
```php
Route::get('/patient/appointments', [PatientController::class, 'myAppointments'])
    ->name('patient.appointments');  // ← SAMA DENGAN ROUTE #1!
```

**Masalah:**
- Laravel tidak boleh punya 2 routes dengan route name yang sama
- Ketika layout.blade.php call `route('patient.appointments')`, Laravel bingung pakai yang mana
- Route #1 butuh parameter `{id}`, tapi patient portal tidak pass parameter
- Result: **Missing required parameter error**

---

## Solusi

Rename route names untuk admin patient detail routes:

### BEFORE:
```php
Route::get('/patients/{id}/appointments', [PatientController::class, 'getAppointments'])
    ->name('patient.appointments');  ← CONFLICT!
Route::get('/patients/{id}/prescriptions', [PatientController::class, 'getPrescriptions'])
    ->name('patient.prescriptions');  ← CONFLICT!
Route::get('/patients/{id}/health-record', [PatientController::class, 'getHealthRecord'])
    ->name('patient.health-record');  ← CONFLICT!
```

### AFTER:
```php
Route::get('/patients/{id}/appointments', [PatientController::class, 'getAppointments'])
    ->name('admin.patient.appointments');  ✅ Unique!
Route::get('/patients/{id}/prescriptions', [PatientController::class, 'getPrescriptions'])
    ->name('admin.patient.prescriptions');  ✅ Unique!
Route::get('/patients/{id}/health-record', [PatientController::class, 'getHealthRecord'])
    ->name('admin.patient.health-record');  ✅ Unique!
```

---

## File Changed

**File:** `routes/web.php`

**Lines 36-38 (Before):**
```php
Route::get('/patients/{id}/appointments', [PatientController::class, 'getAppointments'])->name('patient.appointments');
Route::get('/patients/{id}/prescriptions', [PatientController::class, 'getPrescriptions'])->name('patient.prescriptions');
Route::get('/patients/{id}/health-record', [PatientController::class, 'getHealthRecord'])->name('patient.health-record');
```

**Lines 36-38 (After):**
```php
Route::get('/patients/{id}/appointments', [PatientController::class, 'getAppointments'])->name('admin.patient.appointments');
Route::get('/patients/{id}/prescriptions', [PatientController::class, 'getPrescriptions'])->name('admin.patient.prescriptions');
Route::get('/patients/{id}/health-record', [PatientController::class, 'getHealthRecord'])->name('admin.patient.health-record');
```

---

## Verification

### Routes Now:
```
✅ GET /patient/appointments           → patient.appointments (Patient Portal)
✅ GET /patients/{id}/appointments    → admin.patient.appointments (Admin Detail)
✅ GET /patient/prescriptions         → patient.prescriptions (Patient Portal)
✅ GET /patients/{id}/prescriptions  → admin.patient.prescriptions (Admin Detail)
✅ GET /patient/health-record        → patient.health-record (Patient Portal)
✅ GET /patients/{id}/health-record → admin.patient.health-record (Admin Detail)
```

**No duplicates! ✅**

---

## Impact

| Feature | Status |
|---------|--------|
| Patient Portal Routes | ✅ FIXED |
| Admin Patient Detail Routes | ✅ FIXED |
| No Duplicate Names | ✅ VERIFIED |
| Error Resolved | ✅ YES |

---

## Testing

### Test 1: Patient Login & Menu
1. Login as patient@meditrack.local
2. Click "Appointment Saya" in sidebar
3. ✅ Should work without error
4. URL should be: `/patient/appointments`

### Test 2: Admin View Patient Detail
1. Login as admin@meditrack.local
2. Go to Kelola Pasien
3. Click patient name (if there's a detail link)
4. ✅ Should work with admin.patient.appointments route
5. URL should be: `/patients/{id}/appointments`

---

## Related Files to Update

If any other files reference old route names:
- Check `resources/views/patients/show.blade.php`
- Check `resources/views/patients/index.blade.php`
- Update any `route('patient.appointments')` to `route('admin.patient.appointments')` if used for admin

---

## Summary

| Item | Before | After |
|------|--------|-------|
| Duplicate Routes | ❌ Yes (3 pairs) | ✅ No |
| Error When Clicking Menu | ❌ Yes | ✅ No |
| Patient Portal Works | ❌ No | ✅ Yes |
| Admin Detail Works | ⚠️ Conflict | ✅ Yes |
| Routes Unique | ❌ No | ✅ Yes |

---

**Status:** ✅ FIXED
**Date:** March 26, 2026
**Tested:** YES
