<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    /**
     * Get all doctors with pagination
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            $specialization = $request->input('specialization', '');
            
            $query = User::where('role', 'doctor');
            
            if ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('specialization', 'like', "%$search%");
            }
            
            if ($specialization) {
                $query->where('specialization', $specialization);
            }
            
            $doctors = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Doctors retrieved successfully',
                'data' => $doctors,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving doctors',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get doctor by ID with detailed information
     */
    public function show($id)
    {
        try {
            $doctor = User::where('id', $id)->where('role', 'doctor')->firstOrFail();
            
            $appointments = Appointment::where('doctor_id', $id)->count();
            $patients = Appointment::where('doctor_id', $id)
                ->distinct('patient_id')
                ->count('patient_id');
            $prescriptions = Prescription::where('doctor_id', $id)->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Doctor details retrieved',
                'data' => [
                    'doctor' => $doctor,
                    'statistics' => [
                        'total_appointments' => $appointments,
                        'total_patients' => $patients,
                        'total_prescriptions' => $prescriptions,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Create new doctor
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'specialization' => 'required|string',
                'license_number' => 'required|string|unique:users,license_number',
                'hospital_name' => 'required|string',
                'phone_number' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $doctor = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'doctor',
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'hospital_name' => $request->hospital_name,
                'phone_number' => $request->phone_number,
                'is_verified' => true,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Doctor created successfully',
                'data' => $doctor,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating doctor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update doctor information
     */
    public function update(Request $request, $id)
    {
        try {
            $doctor = User::where('id', $id)->where('role', 'doctor')->firstOrFail();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'specialization' => 'sometimes|string',
                'license_number' => 'sometimes|string|unique:users,license_number,' . $id,
                'hospital_name' => 'sometimes|string',
                'phone_number' => 'sometimes|string',
                'is_active' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $doctor->update($request->only([
                'name', 'email', 'specialization', 'license_number', 
                'hospital_name', 'phone_number', 'is_active'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Doctor updated successfully',
                'data' => $doctor,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating doctor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete doctor
     */
    public function destroy($id)
    {
        try {
            $doctor = User::where('id', $id)->where('role', 'doctor')->firstOrFail();
            
            $doctor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Doctor deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting doctor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get doctor appointments
     */
    public function getAppointments($id)
    {
        try {
            $doctor = User::where('id', $id)->where('role', 'doctor')->firstOrFail();
            
            $status = request()->input('status');
            $query = Appointment::where('doctor_id', $id)->with('patient');
            
            if ($status) {
                $query->where('status', $status);
            }
            
            $appointments = $query->orderBy('appointment_date', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Doctor appointments retrieved',
                'data' => $appointments,
                'count' => $appointments->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving appointments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get doctor prescriptions
     */
    public function getPrescriptions($id)
    {
        try {
            $doctor = User::where('id', $id)->where('role', 'doctor')->firstOrFail();
            
            $prescriptions = Prescription::where('doctor_id', $id)
                ->with('patient')
                ->orderBy('prescribed_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Doctor prescriptions retrieved',
                'data' => $prescriptions,
                'count' => $prescriptions->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving prescriptions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get doctors by specialization
     */
    public function getBySpecialization($specialization)
    {
        try {
            $doctors = User::where('role', 'doctor')
                ->where('specialization', $specialization)
                ->where('is_active', true)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Doctors retrieved by specialization',
                'specialization' => $specialization,
                'data' => $doctors,
                'count' => $doctors->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving doctors',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available doctors by specialization and date
     */
    public function getAvailability($specialization, $date = null)
    {
        try {
            $date = $date ?? now()->format('Y-m-d');
            
            $doctors = User::where('role', 'doctor')
                ->where('specialization', $specialization)
                ->where('is_active', true)
                ->get();

            $availability = [];
            foreach ($doctors as $doctor) {
                $scheduledCount = Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $date)
                    ->count();
                
                $availability[] = [
                    'doctor' => $doctor,
                    'scheduled_appointments' => $scheduledCount,
                    'available_slots' => max(0, 8 - $scheduledCount), // Assume 8 slots per day
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Doctor availability retrieved',
                'specialization' => $specialization,
                'date' => $date,
                'data' => $availability,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving availability',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
