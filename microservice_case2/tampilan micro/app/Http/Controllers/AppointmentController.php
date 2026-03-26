<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Get all appointments (with filtering)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $query = Appointment::query();

            if ($request->user()->role === 'patient') {
                $query->where('patient_id', $request->user()->id);
            } elseif ($request->user()->role === 'doctor') {
                $query->where('doctor_id', $request->user()->id);
            }

            // Filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->date_from && $request->date_to) {
                $query->whereBetween('appointment_date', [
                    $request->date_from,
                    $request->date_to,
                ]);
            }

            $appointments = $query->with(['patient', 'doctor'])
                ->orderBy('appointment_date', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Appointments retrieved successfully',
                'data' => $appointments,
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
     * Get appointment by ID
     */
    public function show($id)
    {
        try {
            $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Appointment retrieved',
                'data' => $appointment,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Create a new appointment
     */
    public function store(Request $request)
    {
        try {
            // Determine patient ID
            $patientId = $request->user()->role === 'patient' 
                ? $request->user()->id 
                : $request->input('patient_id');

            $validator = Validator::make($request->all(), [
                'doctor_id' => 'required|exists:users,id',
                'appointment_date' => 'required|date|after:now',
                'duration_minutes' => 'integer|min:15|max:480|default:30',
                'reason_for_visit' => 'required|string',
                'consultation_type' => 'required|in:in-person,video,phone',
                'meeting_link' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check if doctor exists
            $doctor = User::where('id', $request->doctor_id)
                ->where('role', 'doctor')
                ->firstOrFail();

            // Check for conflicts
            $conflict = Appointment::where('doctor_id', $request->doctor_id)
                ->whereBetween('appointment_date', [
                    $request->appointment_date,
                    now()->parse($request->appointment_date)->addMinutes(
                        $request->input('duration_minutes', 30)
                    ),
                ])
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor has conflicting appointment at this time',
                ], 409);
            }

            $appointment = Appointment::create([
                'patient_id' => $patientId,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'duration_minutes' => $request->input('duration_minutes', 30),
                'reason_for_visit' => $request->reason_for_visit,
                'consultation_type' => $request->consultation_type,
                'meeting_link' => $request->meeting_link,
                'status' => 'scheduled',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'data' => $appointment->load(['patient', 'doctor']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating appointment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update appointment
     */
    public function update(Request $request, $id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'appointment_date' => 'sometimes|date|after:now',
                'duration_minutes' => 'sometimes|integer|min:15|max:480',
                'reason_for_visit' => 'sometimes|string',
                'consultation_type' => 'sometimes|in:in-person,video,phone',
                'status' => 'sometimes|in:scheduled,confirmed,completed,cancelled,rescheduled',
                'meeting_link' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $appointment->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Appointment updated successfully',
                'data' => $appointment->load(['patient', 'doctor']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating appointment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel appointment
     */
    public function cancel($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            if ($appointment->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel completed appointment',
                ], 409);
            }

            $appointment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => request()->input('reason', 'No reason provided'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully',
                'data' => $appointment,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling appointment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm appointment
     */
    public function confirm($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            if ($appointment->status !== 'scheduled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only scheduled appointments can be confirmed',
                ], 409);
            }

            $appointment->update(['status' => 'confirmed']);

            return response()->json([
                'success' => true,
                'message' => 'Appointment confirmed successfully',
                'data' => $appointment,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error confirming appointment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark appointment as completed
     */
    public function complete($id, Request $request)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            if ($appointment->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot complete cancelled appointment',
                ], 409);
            }

            $appointment->update([
                'status' => 'completed',
                'notes' => $request->input('notes'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment marked as completed',
                'data' => $appointment,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing appointment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete appointment
     */
    public function destroy($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            
            $appointment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting appointment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get appointments by date
     */
    public function getByDate($date)
    {
        try {
            $appointments = Appointment::whereDate('appointment_date', $date)
                ->with(['patient', 'doctor'])
                ->orderBy('appointment_date')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Appointments retrieved for date: ' . $date,
                'date' => $date,
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
     * Get available time slots for a doctor
     */
    public function getAvailableSlots($doctorId, $date)
    {
        try {
            $doctor = User::where('id', $doctorId)
                ->where('role', 'doctor')
                ->firstOrFail();

            // Get booked appointments
            $booked = Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', $date)
                ->where('status', '!=', 'cancelled')
                ->get(['appointment_date', 'duration_minutes']);

            // Define working hours (9 AM to 5 PM)
            $workStart = 9;
            $workEnd = 17;
            $slotDuration = 30; // 30 minutes slots
            $slots = [];

            for ($hour = $workStart; $hour < $workEnd; $hour++) {
                for ($minute = 0; $minute < 60; $minute += $slotDuration) {
                    $slotTime = sprintf("%02d:%02d", $hour, $minute);
                    $slotDateTime = now()->parse("$date $slotTime");
                    
                    $isBooked = $booked->some(function ($appointment) use ($slotDateTime, $slotDuration) {
                        $appointmentStart = now()->parse($appointment->appointment_date);
                        $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration_minutes);
                        
                        return $slotDateTime >= $appointmentStart && $slotDateTime < $appointmentEnd;
                    });

                    $slots[] = [
                        'time' => $slotTime,
                        'available' => !$isBooked,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Available slots retrieved',
                'doctor_id' => $doctorId,
                'date' => $date,
                'data' => array_filter($slots, fn($slot) => $slot['available']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving available slots',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
