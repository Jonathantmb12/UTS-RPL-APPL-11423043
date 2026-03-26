# 🔍 VERIFICATION COMMANDS - Patient Role

## ✅ Step-by-Step Verification

---

## 1. Check Routes are Registered

### Command:
```bash
php artisan route:list | grep patient
```

### Expected Output:
```
GET|HEAD  patient/appointments      patient.appointments      PatientController@myAppointments
GET|HEAD  patient/health-record     patient.health-record     PatientController@myHealthRecord
GET|HEAD  patient/prescriptions     patient.prescriptions     PatientController@myPrescriptions
```

### Verification:
- [ ] All 3 patient routes visible
- [ ] Methods are: myAppointments, myPrescriptions, myHealthRecord
- [ ] No errors in output

---

## 2. Check Database for Patient Accounts

### Command:
```bash
php artisan tinker
```

### Then type:
```php
\App\Models\User::where('role', 'patient')->get(['id', 'name', 'email', 'role']);
```

### Expected Output:
```
8 patient records with:
- patient@meditrack.local
- jane@meditrack.local
- bob@meditrack.local
- alice@meditrack.local
- charlie@meditrack.local
- diana@meditrack.local
- edward@meditrack.local
- fiona@meditrack.local
```

### Verification:
- [ ] 8 patient accounts visible
- [ ] All have role = 'patient'
- [ ] Email addresses correct

---

## 3. Check Patient Appointments Exist

### Command:
```bash
php artisan tinker
```

### Then type:
```php
// Get first patient
$patient = \App\Models\User::where('role', 'patient')->first();

// Check appointments
$patient->appointments()->count();  // Should be > 0
$patient->appointments()->get(['id', 'patient_id', 'appointment_date', 'status']);
```

### Expected Output:
```
Patient should have multiple appointments with different statuses
```

### Verification:
- [ ] Patient has appointments
- [ ] Appointments show correct patient_id
- [ ] Various statuses present

---

## 4. Check Patient Prescriptions Exist

### Command:
```bash
php artisan tinker
```

### Then type:
```php
// Get first patient
$patient = \App\Models\User::where('role', 'patient')->first();

// Check prescriptions
$patient->prescriptions()->count();  // Should be > 0
$patient->prescriptions()->get(['id', 'patient_id', 'medication_name', 'dosage']);
```

### Expected Output:
```
Patient should have multiple prescriptions
```

### Verification:
- [ ] Patient has prescriptions
- [ ] Prescriptions show correct patient_id
- [ ] Medication details visible

---

## 5. Check Patient Health Records Exist

### Command:
```bash
php artisan tinker
```

### Then type:
```php
// Get first patient
$patient = \App\Models\User::where('role', 'patient')->first();

// Check health record
\App\Models\ElectronicHealthRecord::where('patient_id', $patient->id)->first();
```

### Expected Output:
```
Health record with vital signs, medical history, etc.
```

### Verification:
- [ ] Patient has health record
- [ ] Record contains expected fields
- [ ] Record linked to correct patient

---

## 6. Check Controller Methods Exist

### Command:
```bash
php artisan tinker
```

### Then type:
```php
// Check if methods exist
$controller = new \App\Http\Controllers\PatientController;
method_exists($controller, 'myAppointments');    // Should return true
method_exists($controller, 'myPrescriptions');   // Should return true
method_exists($controller, 'myHealthRecord');    // Should return true
```

### Expected Output:
```
true
true
true
```

### Verification:
- [ ] All 3 methods exist
- [ ] Methods are callable

---

## 7. Check Views Exist

### Command:
```bash
ls resources/views/patient/
```

Or on Windows:
```bash
dir resources\views\patient\
```

### Expected Output:
```
appointments.blade.php
health-record.blade.php
prescriptions.blade.php
```

### Verification:
- [ ] All 3 views exist
- [ ] Files are in correct directory
- [ ] Files have .blade.php extension

---

## 8. Check Login Page Updated

### Command:
```bash
grep -n "patient@meditrack.local" resources/views/auth/login.blade.php
```

### Expected Output:
```
Line number showing "patient@meditrack.local" in login page
```

### Verification:
- [ ] Patient email visible in login page
- [ ] Demo credentials section includes patient

---

## 9. Run Server and Check Browser

### Command:
```bash
php artisan serve --port=8000
```

Then open: `http://127.0.0.1:8000/login`

### Verification:
- [ ] Login page loads without errors
- [ ] Patient credentials visible in demo section
- [ ] Emoji icons visible (👤, 👨‍⚕️, 🩺, 💊)

---

## 10. Test Patient Login

### Steps:
1. Open: `http://127.0.0.1:8000/login`
2. Enter:
   - Email: `patient@meditrack.local`
   - Password: `password123`
3. Click "Sign In"

### Expected Result:
- [ ] Redirect to `/dashboard`
- [ ] Navbar shows "Patient User"
- [ ] Sidebar shows patient menu
- [ ] No errors in console

---

## 11. Test Patient Menu Items

### Verification:
- [ ] Sidebar shows "📅 Appointment Saya"
- [ ] Sidebar shows "💊 Resep Saya"
- [ ] Sidebar shows "📋 Riwayat Kesehatan"
- [ ] All items are clickable

---

## 12. Test Patient Appointments Page

### Steps:
1. Click "Appointment Saya" in sidebar
2. Wait for page to load

### Expected Result:
- [ ] URL is `/patient/appointments`
- [ ] Page loads without 404 error
- [ ] Appointments table/list visible
- [ ] Shows doctor name, date, status
- [ ] Data is for current patient only

---

## 13. Test Patient Prescriptions Page

### Steps:
1. Click "Resep Saya" in sidebar
2. Wait for page to load

### Expected Result:
- [ ] URL is `/patient/prescriptions`
- [ ] Page loads without 404 error
- [ ] Prescriptions table/list visible
- [ ] Shows medication, dosage, doctor, dates
- [ ] Data is for current patient only

---

## 14. Test Patient Health Record Page

### Steps:
1. Click "Riwayat Kesehatan" in sidebar
2. Wait for page to load

### Expected Result:
- [ ] URL is `/patient/health-record`
- [ ] Page loads without 404 error
- [ ] Health record displayed
- [ ] Shows vital signs, medical history, medications, allergies, etc.
- [ ] Data is for current patient only

---

## 15. Test Multiple Patient Accounts

### Steps:
1. Logout (if logged in)
2. Login with `jane@meditrack.local` / `password123`
3. Go to `/patient/appointments`
4. Note the appointments
5. Logout
6. Login with `bob@meditrack.local` / `password123`
7. Go to `/patient/appointments`
8. Verify different appointments than jane

### Expected Result:
- [ ] Each patient sees own data only
- [ ] Cannot see other patient data
- [ ] Data changes correctly when switching patients

---

## 16. Test Role-Based Access Control

### Steps:
1. Login as admin
2. Try to access `/patient/appointments`

### Expected Result:
- [ ] Cannot access (denied by middleware)
- [ ] See error page or redirected
- [ ] Patient menu not in sidebar

---

## 17. Check for Errors

### Command:
```bash
php artisan route:list > /dev/null 2>&1 && echo "No errors"
```

### Expected Output:
```
No errors
```

### Verification:
- [ ] No PHP syntax errors
- [ ] No Laravel errors
- [ ] Routes load cleanly

---

## 🎯 Summary Checklist

### ✅ All verification points:
- [ ] 3 patient routes registered
- [ ] 8 patient accounts in database
- [ ] Patient data (appointments, prescriptions, health records) exists
- [ ] 3 controller methods exist
- [ ] 3 view files exist
- [ ] Login page shows patient credentials
- [ ] Server runs without errors
- [ ] Patient can login
- [ ] Patient menu visible
- [ ] Can view appointments
- [ ] Can view prescriptions
- [ ] Can view health record
- [ ] Multiple patients have separate data
- [ ] Role-based access working
- [ ] No errors in system

---

## 🎉 If All Checks Pass

System is **PRODUCTION READY**!

Patient role is fully functional and secure.

All features working as expected.

Ready for deployment.

---

**Verification Date:** March 26, 2026
**Status:** ✅ READY TO VERIFY
