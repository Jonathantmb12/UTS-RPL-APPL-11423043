# Laravel Integration with Microservices

## Overview

Proyek Laravel Anda sekarang berfungsi sebagai **UI/Frontend** yang berkomunikasi dengan 6+ microservices berbasis Go.

---

## 🔄 Migration Path

### From: Monolithic Laravel
```
Laravel Application
├── Controllers (all logic here)
├── Models (all data here)
└── Database (single database)
```

### To: Microservices Architecture
```
Laravel UI (Frontend only)
├── Routes (call Go APIs)
├── Controllers (call microservices)
├── Views (Blade templates)
└── Services (HTTP clients)

+ Go Microservices
├── Auth Service (8001)
├── Patient Service (8002)
├── Doctor Service (8003)
├── Appointment Service (8004)
├── Prescription Service (8005)
├── Pharmacy Service (8006)
└── [Each with own database + logic]
```

---

## 📝 Step-by-Step Laravel Integration

### 1. Create MicroserviceClient Helper

**File:** `app/Services/MicroserviceClient.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class MicroserviceClient
{
    protected $baseUrls = [
        'auth'        => 'http://localhost:8001/api',
        'patient'     => 'http://localhost:8002/api',
        'doctor'      => 'http://localhost:8003/api',
        'appointment' => 'http://localhost:8004/api',
        'prescription'=> 'http://localhost:8005/api',
        'pharmacy'    => 'http://localhost:8006/api',
    ];

    protected $timeout = 30;

    /**
     * Get API token from session or user
     */
    protected function getToken()
    {
        // Get from session (after login)
        if (session()->has('api_token')) {
            return session('api_token');
        }
        
        // Or from authenticated user
        if (Auth::check() && Auth::user()->api_token) {
            return Auth::user()->api_token;
        }

        return null;
    }

    /**
     * GET request to microservice
     */
    public function get($service, $endpoint, $params = [])
    {
        $url = "{$this->baseUrls[$service]}{$endpoint}";
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $response = Http::timeout($this->timeout)
            ->withToken($this->getToken())
            ->get($url);

        if ($response->failed()) {
            throw new \Exception("API Error: " . $response->body());
        }

        return $response->json();
    }

    /**
     * POST request to microservice
     */
    public function post($service, $endpoint, $data = [])
    {
        $response = Http::timeout($this->timeout)
            ->withToken($this->getToken())
            ->post("{$this->baseUrls[$service]}{$endpoint}", $data);

        if ($response->failed()) {
            throw new \Exception("API Error: " . $response->body());
        }

        return $response->json();
    }

    /**
     * PUT request to microservice
     */
    public function put($service, $endpoint, $data = [])
    {
        $response = Http::timeout($this->timeout)
            ->withToken($this->getToken())
            ->put("{$this->baseUrls[$service]}{$endpoint}", $data);

        if ($response->failed()) {
            throw new \Exception("API Error: " . $response->body());
        }

        return $response->json();
    }

    /**
     * DELETE request to microservice
     */
    public function delete($service, $endpoint)
    {
        $response = Http::timeout($this->timeout)
            ->withToken($this->getToken())
            ->delete("{$this->baseUrls[$service]}{$endpoint}");

        if ($response->failed()) {
            throw new \Exception("API Error: " . $response->body());
        }

        return $response->json();
    }
}
```

### 2. Update AuthController

**File:** `app/Http/Controllers/AuthController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\MicroserviceClient;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new MicroserviceClient();
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        try {
            // Call Auth Service
            $response = $this->client->post('auth', '/login', $credentials);

            // Store user data in session
            session([
                'user_id'   => $response['id'],
                'user_name' => $response['name'],
                'user_email'=> $response['email'],
                'user_role' => $response['role'],
                'api_token' => $response['token'],
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Login successful!');

        } catch (\Exception $e) {
            return back()->with('error', 'Invalid credentials');
        }
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')
            ->with('success', 'Logged out successfully');
    }

    /**
     * Show register form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:patient,doctor,pharmacist',
            'phone_number' => 'nullable|string',
            'date_of_birth'=> 'nullable|date',
        ]);

        try {
            // Call Auth Service to register
            $response = $this->client->post('auth', '/register', $data);

            // Auto-login
            session([
                'user_id'   => $response['id'],
                'user_name' => $response['name'],
                'user_email'=> $response['email'],
                'user_role' => $response['role'],
                'api_token' => $response['token'],
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Registration successful!');

        } catch (\Exception $e) {
            return back()->withError('Registration failed: ' . $e->getMessage());
        }
    }
}
```

### 3. Update PatientController

**File:** `app/Http/Controllers/PatientController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\MicroserviceClient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new MicroserviceClient();
    }

    /**
     * List all patients
     */
    public function index(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $search = $request->get('q');

            if ($search) {
                // Search patients
                $response = $this->client->get('patient', '/patients/search', [
                    'q' => $search
                ]);
                $patients = $response;
            } else {
                // Get paginated list
                $response = $this->client->get('patient', '/patients', [
                    'page' => $page
                ]);
                $patients = $response['data'];
            }

            return view('patients.index', compact('patients'));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch patients');
        }
    }

    /**
     * Show patient detail
     */
    public function show($id)
    {
        try {
            $patient = $this->client->get('patient', "/patients/{$id}");

            return view('patients.show', compact('patient'));

        } catch (\Exception $e) {
            return back()->with('error', 'Patient not found');
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store new patient
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'            => 'required',
            'name'               => 'required|string',
            'email'              => 'required|email',
            'phone_number'       => 'required|string',
            'date_of_birth'      => 'required|date',
            'gender'             => 'required|in:male,female,other',
            'address'            => 'required|string',
            'emergency_contact'  => 'required|string',
            'blood_type'         => 'required|in:A,B,AB,O',
            'allergies'          => 'nullable|string',
        ]);

        try {
            $response = $this->client->post('patient', '/patients', $data);

            return redirect()->route('patients.show', $response['id'])
                ->with('success', 'Patient created successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create patient');
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $patient = $this->client->get('patient', "/patients/{$id}");
            return view('patients.edit', compact('patient'));

        } catch (\Exception $e) {
            return back()->with('error', 'Patient not found');
        }
    }

    /**
     * Update patient
     */
    public function update($id, Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string',
            'phone_number'  => 'required|string',
            'address'       => 'required|string',
        ]);

        try {
            $response = $this->client->put('patient', "/patients/{$id}", $data);

            return redirect()->route('patients.show', $id)
                ->with('success', 'Patient updated successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update patient');
        }
    }

    /**
     * Delete patient
     */
    public function destroy($id)
    {
        try {
            $this->client->delete('patient', "/patients/{$id}");

            return redirect()->route('patients.index')
                ->with('success', 'Patient deleted successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete patient');
        }
    }
}
```

Similar patterns apply to:
- `DoctorController` (uses doctor-service)
- `AppointmentController` (uses appointment-service)
- `PrescriptionController` (uses prescription-service)
- `PharmacyDetailController` (uses pharmacy-service)

### 4. Update Routes

**File:** `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PharmacyDetailController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth.session'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Patient routes
    Route::resource('patients', PatientController::class);

    // Doctor routes
    Route::resource('doctors', DoctorController::class);

    // Appointment routes
    Route::resource('appointments', AppointmentController::class);

    // Prescription routes
    Route::resource('prescriptions', PrescriptionController::class);

    // Pharmacy routes
    Route::get('/pharmacy/inventory', [PharmacyDetailController::class, 'inventory'])
        ->name('pharmacy.inventory');
    Route::post('/pharmacy/inventory', [PharmacyDetailController::class, 'createInventory'])
        ->name('pharmacy.inventory.store');
});
```

### 5. Create Auth Middleware

**File:** `app/Http/Middleware/CheckSession.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('api_token')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
```

Register in `bootstrap/app.php`:
```php
->withMiddlewareGroup('auth.session', [
    \App\Http\Middleware\CheckSession::class,
])
```

---

## 🎯 Updated View Structure

Your Blade views remain the same, but forms now POST to local routes which then call microservices:

### Example: Create Patient Form

**File:** `resources/views/patients/create.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Patient</h1>

    <form action="{{ route('patients.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone_number" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Blood Type</label>
            <select name="blood_type" class="form-control" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
                <option value="O">O</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
```

---

## 🔐 Securing API Calls

Add error handling & retry logic:

```php
public function getPatientWithRetry($id, $maxRetries = 3)
{
    $attempts = 0;

    while ($attempts < $maxRetries) {
        try {
            return $this->client->get('patient', "/patients/{$id}");
        } catch (\Exception $e) {
            $attempts++;
            if ($attempts >= $maxRetries) {
                \Log::error("Failed to fetch patient {$id}: " . $e->getMessage());
                throw $e;
            }
            sleep(1); // Wait before retry
        }
    }
}
```

---

## ✅ Testing Integration

After implementing changes:

```bash
# 1. Start all Go services
# 2. Start Laravel
php artisan serve

# 3. Test login
# Go to http://localhost:8000/login
# Use: admin@meditrack.com / password

# 4. Test patient list
# Go to http://localhost:8000/patients

# 5. Check logs
tail -f storage/logs/laravel.log
```

---

## 📊 Migration Checklist

- [ ] Create `MicroserviceClient` helper
- [ ] Update `AuthController` for login/logout
- [ ] Update `PatientController`
- [ ] Update `DoctorController`
- [ ] Update `AppointmentController`
- [ ] Update `PrescriptionController`
- [ ] Update `PharmacyDetailController`
- [ ] Create `CheckSession` middleware
- [ ] Update `routes/web.php`
- [ ] Test all controllers with APIs
- [ ] Add error handling & logging
- [ ] Update views if needed

---

## 🐛 Troubleshooting

### "API Connection Failed"
- Ensure all Go services are running
- Check ports 8001-8006 are not blocked
- Verify `.env` files in each service

### "Invalid Token"
- Make sure token is stored in session after login
- Check token is passed in Authorization header
- Verify JWT secret matches between services

### "CORS Error"
- All services have CORS middleware enabled
- No additional configuration needed

### "Database Error"
- Create all 7 databases first
- Run migrations in each service
- Check MySQL credentials in .env

---

**Status:** Your Laravel application is now a microservices frontend! 🎉
