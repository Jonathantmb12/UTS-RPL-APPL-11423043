<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PrescriptionDetailController;
use App\Http\Controllers\PharmacyDetailController;

// ===== PUBLIC API ROUTES =====
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// ===== NOTE: For production, add API authentication middleware here =====
// Currently using web routes for the management interface
// API authentication can be added later with proper token management
     