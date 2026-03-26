# 🏥 MediTrack - Healthcare Management System

Sistem manajemen kesehatan terintegrasi lengkap dengan fitur appointment scheduling, patient management, prescription tracking, dan pharmacy inventory management.

## 📋 Persyaratan Sistem

- PHP 8.1+
- MySQL 5.7+ atau MariaDB
- Composer
- Node.js & npm
- Laravel 11

## 🚀 Instalasi & Setup

### 1. Clone & Setup Project
```bash
cd "d:\semester 6\tugas\tugas"
composer install
npm install
```

### 2. Konfigurasi Database
```bash
# Buat file .env dari .env.example
cp .env.example .env

# Edit .env dan sesuaikan:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=applcase1
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Run Migrations & Seeding
```bash
# Migration + Seeding (dengan 51 demo records)
php artisan migrate:fresh --seed

# Atau hanya migration
php artisan migrate

# Atau hanya seeding
php artisan db:seed
```

### 5. Build Assets
```bash
npm run build
# atau untuk development
npm run dev
```

### 6. Start Development Server
```bash
php artisan serve
```

Server akan berjalan di: **http://localhost:8000**

## 🔐 Demo Accounts

Login ke sistem menggunakan akun demo berikut:

### Admin
```
Email: admin@meditrack.com
Password: password
Role: Admin
```

### Doctors (4)
```
Email: doctor1@meditrack.com - doctor4@meditrack.com
Password: password (semua)
Role: Doctor
```

### Pharmacists (3)
```
Email: pharmacist1@meditrack.com - pharmacist3@meditrack.com
Password: password (semua)
Role: Pharmacist
```

### Patients (8)
```
Email: patient1@meditrack.com - patient8@meditrack.com
Password: password (semua)
Role: Patient
```

## 📱 Fitur Utama

### 👥 Patient Management
- Tambah/Edit/Hapus pasien
- Search dan filter by blood type
- View profil lengkap dengan tabs (Personal, Health, Appointments, Prescriptions)

### 👨⚕️ Doctor Management
- Kelola daftar dokter
- Filter by specialization
- Lihat appointment & prescription history

### 📅 Appointment Scheduling
- Buat appointment dengan conflict detection
- Available time slots: 30-min intervals, 9AM-5PM
- Status tracking: Scheduled → Confirmed → Completed/Cancelled

### 💊 Prescription Management
- Resep obat dengan auto-expiration date
- Track status: Active, Soon Expiring, Expired
- Filter by medication, status, doctor, patient

### 🏪 Pharmacy Management
- Inventory obat dengan stock tracking
- Low-stock alerts dan reorder notifications
- Prescription orders management

### 📊 Dashboard Analytics
- KPI cards (Patients, Doctors, Appointments, Prescriptions)
- Recent activities timeline
- Upcoming appointments preview

## 🌐 URL Endpoints

### Web Routes
```
GET  /login                           → Login page
GET  /dashboard                       → Admin dashboard
GET  /patients, /doctors              → List views
GET  /appointments, /prescriptions    → Management pages
GET  /pharmacy/inventory              → Inventory management
GET  /pharmacy/orders                 → Order management
GET  /pharmacy/low-stock              → Low-stock alerts
```

### API Endpoints (50+)
- 6 Patient endpoints
- 8 Doctor endpoints
- 10 Appointment endpoints
- 8 Prescription endpoints
- 10 Pharmacy endpoints
- 3 Auth endpoints
- 3 EHR endpoints
- 3 Payment endpoints
- 8 Analytics endpoints

## 🎨 Design & UI

### Responsive Design
- ✅ Desktop (1200px+)
- ✅ Tablet (768px - 1199px)
- ✅ Mobile (< 768px)

### Tech Stack
- Bootstrap 5.3
- Bootstrap Icons (1.11.0)
- Custom gradient styling
- Responsive sidebar navigation

## 🔒 Security Features

- CSRF Protection
- SQL Injection Prevention
- Password Hashing (bcrypt)
- Role-based Authorization
- API Sanctum Authentication
- Input Validation

## 📚 Documentation

Untuk dokumentasi lengkap, lihat:
- `COMPLETE_IMPLEMENTATION.md` - Fitur & API endpoints
- `IMPLEMENTATION_SUMMARY.md` - Technical overview
- `MEDITRACK_README.md` - Project details

---

**Status**: ✅ COMPLETE & READY FOR PRODUCTION


- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
