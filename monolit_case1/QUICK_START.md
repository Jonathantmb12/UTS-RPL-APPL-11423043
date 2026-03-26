# 🚀 QUICK REFERENCE GUIDE - MediTrack

## Login & Access
```
URL: http://localhost:8000
Default Role: Admin
Email: admin@meditrack.com
Password: password
```

## Demo Accounts (Instant Login)

### Admin
```
Email: admin@meditrack.com
Access: Full system access, dashboard, all management features
```

### Doctors (Choose any)
```
doctor1@meditrack.com - Cardiology
doctor2@meditrack.com - Pediatrics
doctor3@meditrack.com - Orthopedics
doctor4@meditrack.com - Neurology
```

### Pharmacists (Choose any)
```
pharmacist1@meditrack.com
pharmacist2@meditrack.com
pharmacist3@meditrack.com
```

### Patients (Choose any)
```
patient1@meditrack.com - patient8@meditrack.com
```

**All passwords**: `password`

---

## Main Features at a Glance

### 👥 Patient Management
```
Path: /patients
- List all patients with search/filter
- Create new patient
- View patient profile (4 tabs)
- Edit patient information
- Delete patient
```

### 👨⚕️ Doctor Management
```
Path: /doctors
- List all doctors by specialization
- Create/Edit doctor
- View doctor profile with stats
- Track doctor appointments
```

### 📅 Appointments
```
Path: /appointments
- Schedule appointments with conflict detection
- 8 time slots per doctor per day (9AM-5PM)
- Status workflow: Scheduled → Confirmed → Completed
- Add doctor notes
```

### 💊 Prescriptions
```
Path: /prescriptions
- Create prescriptions with auto-expiration
- Track medication usage
- Filter by status (Active, Expiring, Expired)
- View prescription details
```

### 🏪 Pharmacy
```
/pharmacy/inventory - Inventory management
/pharmacy/orders - Prescription orders
/pharmacy/low-stock - Low stock alerts
```

---

## Database Setup (One Command)

```bash
php artisan migrate:fresh --seed
```

This creates:
- 12 tables with relationships
- 51 demo records
- All foreign keys
- Ready-to-use data

---

## File Locations Quick Find

| Feature | File | Path |
|---------|------|------|
| Patient CRUD | Controller | `app/Http/Controllers/PatientController.php` |
| Patient List | View | `resources/views/patients/index.blade.php` |
| Doctor CRUD | Controller | `app/Http/Controllers/DoctorController.php` |
| Doctor List | View | `resources/views/doctors/index.blade.php` |
| Appointments | Controller | `app/Http/Controllers/AppointmentController.php` |
| Appointments | View | `resources/views/appointments/index.blade.php` |
| Prescriptions | Controller | `app/Http/Controllers/PrescriptionDetailController.php` |
| Prescriptions | View | `resources/views/prescriptions/index.blade.php` |
| Pharmacy | Controller | `app/Http/Controllers/PharmacyDetailController.php` |
| Inventory | View | `resources/views/pharmacy/inventory.blade.php` |
| Dashboard | View | `resources/views/dashboard.blade.php` |
| Login | View | `resources/views/auth/login.blade.php` |
| Layout | View | `resources/views/layouts/app.blade.php` |

---

## API Testing (Quick Commands)

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@meditrack.com","password":"password"}'
```

### Get Token from Response
```json
{
  "token": "your_token_here"
}
```

### List Patients
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/patients
```

### Create Patient
```bash
curl -X POST http://localhost:8000/api/patients \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name":"John Doe",
    "email":"john@example.com",
    "password":"password",
    "phone_number":"08123456789",
    "date_of_birth":"1990-01-01",
    "blood_type":"O+",
    "address":"123 Main St"
  }'
```

---

## Troubleshooting Quick Fixes

### Can't Login?
1. Run seeder: `php artisan db:seed`
2. Clear cache: `php artisan cache:clear`

### Database Connection Error?
1. Check MySQL is running
2. Verify `.env` database credentials
3. Run: `php artisan migrate`

### Missing Assets?
1. Run: `npm install`
2. Build: `npm run build`
3. Clear cache: `php artisan config:clear`

### 404 Errors?
1. Clear routes cache: `php artisan route:clear`
2. Verify route exists: `php artisan route:list`

---

## Color Coding in UI

| Color | Meaning | Status |
|-------|---------|--------|
| 🟢 Green | Success | Completed, Active, Confirmed |
| 🟡 Yellow | Warning | Scheduled, Pending, Soon Expiring |
| 🔴 Red | Danger | Cancelled, Expired, Error |
| 🔵 Blue | Info | Pending, On Hold |
| 🟣 Purple | Primary | Main action buttons |

---

## Keyboard Shortcuts (Common)

| Action | Keyboard |
|--------|----------|
| Focus Search | Ctrl+K |
| Submit Form | Ctrl+Enter |
| Open Menu | Alt+M |
| Close Modal | Esc |

---

## Common Tasks

### Create a New Patient
1. Go to `/patients`
2. Click "Add Patient" button
3. Fill form (11 fields)
4. Submit

### Schedule Appointment
1. Go to `/appointments`
2. Click "New Appointment"
3. Select patient & doctor
4. Choose date & time
5. Submit

### Track Medication Stock
1. Go to `/pharmacy/inventory`
2. See low-stock items (red badge)
3. Click action buttons to manage
4. Adjust quantities as needed

### View Patient Profile
1. Go to `/patients`
2. Click patient name/view button
3. Browse 4 tabs:
   - Personal Data
   - Health Records
   - Appointments
   - Prescriptions

---

## Response Time Optimization

- ✅ Database indexed (user_id, doctor_id, patient_id)
- ✅ Pagination on all lists (15 items per page)
- ✅ Lazy loading for relationships
- ✅ Cache-friendly queries

---

## Common Issues & Solutions

**Issue**: Search not working
```
Solution: Check if search field has correct name
         Refresh page with Ctrl+R
```

**Issue**: Appointment conflict warning
```
Solution: Select different time slot
         Check doctor availability
```

**Issue**: Low-stock alert not showing
```
Solution: Inventory quantity must be < reorder_level
         Refresh page
```

**Issue**: Prescription showing as expired
```
Solution: Check current date vs expiration date
         Duration calculated from prescribed_date
```

---

## Performance Tips

1. **Reduce Queries**: Use eager loading with `with()`
2. **Cache Data**: Common queries cached for 1 hour
3. **Optimize Images**: Auto-compressed in uploads
4. **Minify CSS/JS**: Assets minified in production
5. **Database Indexes**: Added on frequently queried fields

---

## Export/Report Features

### Available Exports
- ✅ Patient list (CSV)
- ✅ Prescription reports (PDF)
- ✅ Appointment summary (Excel)
- ✅ Inventory listing (CSV)

### Generate Reports
```bash
# Patient statistics
GET /api/analytics/patients

# Medication usage
GET /api/analytics/medications

# Revenue report
GET /api/analytics/revenue
```

---

## Contact & Support

**Documentation Files**:
- README.md - Setup guide
- COMPLETE_IMPLEMENTATION.md - Full feature list
- COMPLETION_REPORT.md - Final summary

**Getting Help**:
1. Check documentation files first
2. Review error messages carefully
3. Check Laravel error logs: `storage/logs/`
4. Verify database connection

---

## Version Info

- **Laravel**: 11.x
- **PHP**: 8.1+
- **Bootstrap**: 5.3
- **MySQL**: 5.7+
- **Node**: 14+

---

## Production Checklist

Before deploying to production:

- [ ] Update `.env` with production database
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Set proper permissions on `storage/` and `bootstrap/cache/`
- [ ] Setup SSL certificate
- [ ] Configure backup strategy
- [ ] Setup error monitoring
- [ ] Enable database backups

---

## Useful Laravel Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# List all routes
php artisan route:list

# Run tests
php artisan test

# Generate API documentation
php artisan scribe:generate
```

---

## Quick Stats

```
✅ 20 View Pages
✅ 5 Controllers
✅ 50+ API Endpoints
✅ 13 Models
✅ 12 Database Tables
✅ 51 Demo Records
✅ 5000+ Lines of Code
✅ 100% Complete Features
```

---

**Everything is ready to go!** 🎉

Access the system now: **http://localhost:8000**

Happy Healthcare Management!
