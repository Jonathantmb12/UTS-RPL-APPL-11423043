<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PrescriptionDetailController;
use App\Http\Controllers\PharmacyDetailController;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'webLogin'])->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->middleware('auth')->name('logout');

// Dashboard routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ===== PATIENT ROUTES (Admin) =====
    Route::resource('patients', PatientController::class)->middleware('role:admin');
    Route::get('/patients/{id}/appointments', [PatientController::class, 'getAppointments'])->name('admin.patient.appointments');
    Route::get('/patients/{id}/prescriptions', [PatientController::class, 'getPrescriptions'])->name('admin.patient.prescriptions');
    Route::get('/patients/{id}/health-record', [PatientController::class, 'getHealthRecord'])->name('admin.patient.health-record');

    // ===== DOCTOR ROUTES (Admin) =====
    Route::resource('doctors', DoctorController::class)->middleware('role:admin');
    Route::get('/doctors/{id}/appointments', [DoctorController::class, 'getAppointments'])->name('doctor.appointments');
    Route::get('/doctors/{id}/prescriptions', [DoctorController::class, 'getPrescriptions'])->name('doctor.prescriptions');
    Route::get('/doctors/{id}/patients', function ($id) {
        return view('doctors.patients');
    })->name('doctor.patients');

    // ===== APPOINTMENT ROUTES =====
    Route::resource('appointments', AppointmentController::class)->middleware('role:admin');
    Route::get('/appointments/{id}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');

    // ===== PRESCRIPTION ROUTES =====
    Route::resource('prescriptions', PrescriptionDetailController::class)->middleware('role:admin,doctor');

    // ===== PATIENT PORTAL ROUTES (Patient only) =====
    Route::prefix('patient')->middleware('role:patient')->group(function () {
        Route::get('/appointments', [PatientController::class, 'myAppointments'])->name('patient.appointments');
        Route::get('/prescriptions', [PatientController::class, 'myPrescriptions'])->name('patient.prescriptions');
        Route::get('/health-record', [PatientController::class, 'myHealthRecord'])->name('patient.health-record');
    });

    // ===== PHARMACY ROUTES (Pharmacist) =====
    Route::prefix('pharmacy')->middleware('role:pharmacist')->group(function () {
        Route::get('/inventory', [PharmacyDetailController::class, 'getInventory'])->name('pharmacy.inventory');
        Route::get('/orders', function () {
            return view('pharmacy.orders');
        })->name('pharmacy.orders');
        Route::get('/low-stock', [PharmacyDetailController::class, 'getLowStock'])->name('pharmacy.low-stock');
        Route::post('/add-medication', [PharmacyDetailController::class, 'addMedication'])->name('pharmacy.add-medication');
    });
});
