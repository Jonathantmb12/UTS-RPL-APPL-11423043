<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrescriptionDetailController extends Controller
{
    /**
     * Get all prescriptions with filtering
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $query = Prescription::query();

            // Filter by patient
            if ($request->patient_id) {
                $query->where('patient_id', $request->patient_id);
            }

            // Filter by doctor
            if ($request->doctor_id) {
                $query->where('doctor_id', $request->doctor_id);
            }

            // Filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Search by medication name
            if ($request->search) {
                $query->where('medication_name', 'like', "%{$request->search}%");
            }

            $prescriptions = $query->with(['patient', 'doctor', 'appointment'])
                ->orderBy('prescribed_date', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Prescriptions retrieved successfully',
                'data' => $prescriptions,
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
     * Get prescription by ID
     */
    public function show($id)
    {
        try {
            $prescription = Prescription::with(['patient', 'doctor', 'appointment'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Prescription retrieved',
                'data' => $prescription,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Prescription not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Create new prescription
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:users,id',
                'doctor_id' => 'required|exists:users,id',
                'medication_name' => 'required|string',
                'dosage' => 'required|string',
                'frequency' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'duration_days' => 'required|integer|min:1',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'side_effects_warning' => 'nullable|string',
                'appointment_id' => 'nullable|exists:appointments,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Verify patient and doctor exist
            User::where('id', $request->patient_id)->where('role', 'patient')->firstOrFail();
            User::where('id', $request->doctor_id)->where('role', 'doctor')->firstOrFail();

            $prescription = Prescription::create([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'appointment_id' => $request->appointment_id,
                'medication_name' => $request->medication_name,
                'dosage' => $request->dosage,
                'frequency' => $request->frequency,
                'quantity' => $request->quantity,
                'duration_days' => $request->duration_days,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'side_effects_warning' => $request->side_effects_warning,
                'prescribed_date' => now(),
                'expiration_date' => now()->addDays($request->duration_days),
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prescription created successfully',
                'data' => $prescription->load(['patient', 'doctor']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating prescription',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update prescription
     */
    public function update(Request $request, $id)
    {
        try {
            $prescription = Prescription::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'medication_name' => 'sometimes|string',
                'dosage' => 'sometimes|string',
                'frequency' => 'sometimes|string',
                'quantity' => 'sometimes|integer|min:1',
                'duration_days' => 'sometimes|integer|min:1',
                'description' => 'sometimes|string',
                'instructions' => 'sometimes|string',
                'side_effects_warning' => 'sometimes|string',
                'status' => 'sometimes|in:active,inactive,expired,cancelled',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $prescription->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Prescription updated successfully',
                'data' => $prescription,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating prescription',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete prescription
     */
    public function destroy($id)
    {
        try {
            $prescription = Prescription::findOrFail($id);
            
            $prescription->delete();

            return response()->json([
                'success' => true,
                'message' => 'Prescription deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting prescription',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get prescriptions by patient
     */
    public function getByPatient($patientId)
    {
        try {
            User::where('id', $patientId)->where('role', 'patient')->firstOrFail();

            $prescriptions = Prescription::where('patient_id', $patientId)
                ->with(['doctor', 'appointment'])
                ->orderBy('prescribed_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Patient prescriptions retrieved',
                'patient_id' => $patientId,
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
     * Get prescriptions by doctor
     */
    public function getByDoctor($doctorId)
    {
        try {
            User::where('id', $doctorId)->where('role', 'doctor')->firstOrFail();

            $prescriptions = Prescription::where('doctor_id', $doctorId)
                ->with(['patient', 'appointment'])
                ->orderBy('prescribed_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Doctor prescriptions retrieved',
                'doctor_id' => $doctorId,
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
     * Get active prescriptions
     */
    public function getActive()
    {
        try {
            $prescriptions = Prescription::where('status', 'active')
                ->where('expiration_date', '>', now())
                ->with(['patient', 'doctor'])
                ->orderBy('prescribed_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Active prescriptions retrieved',
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
     * Get expired prescriptions
     */
    public function getExpired()
    {
        try {
            $prescriptions = Prescription::where(function ($query) {
                $query->where('status', 'expired')
                    ->orWhere('expiration_date', '<', now());
            })->with(['patient', 'doctor'])
                ->orderBy('expiration_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Expired prescriptions retrieved',
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
}
