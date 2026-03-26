<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Get all prescriptions
     */
    public function index(Request $request)
    {
        $query = Prescription::query();

        if ($request->user()->role === 'patient') {
            $query->where('patient_id', $request->user()->id);
        } elseif ($request->user()->role === 'doctor') {
            $query->where('doctor_id', $request->user()->id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $prescriptions = $query->with(['patient', 'doctor'])->paginate(15);

        return response()->json($prescriptions);
    }

    /**
     * Create new prescription
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'doctor') {
            return response()->json(['message' => 'Only doctors can issue prescriptions'], 403);
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'medication_name' => 'required|string',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'instructions' => 'string|max:1000',
            'side_effects_warning' => 'string|max:1000',
            'appointment_id' => 'exists:appointments,id',
        ]);

        $prescription = Prescription::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $request->user()->id,
            'medication_name' => $validated['medication_name'],
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'],
            'quantity' => $validated['quantity'],
            'duration_days' => $validated['duration_days'],
            'instructions' => $validated['instructions'] ?? null,
            'side_effects_warning' => $validated['side_effects_warning'] ?? null,
            'appointment_id' => $validated['appointment_id'] ?? null,
            'prescribed_date' => now(),
            'expiration_date' => now()->addDays($validated['duration_days']),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Prescription created successfully',
            'prescription' => $prescription->load(['patient', 'doctor']),
        ], 201);
    }

    /**
     * Get prescription details
     */
    public function show(Prescription $prescription)
    {
        return response()->json($prescription->load(['patient', 'doctor', 'prescriptionOrders']));
    }

    /**
     * Update prescription
     */
    public function update(Request $request, Prescription $prescription)
    {
        if ($request->user()->id !== $prescription->doctor_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'in:active,inactive,expired,cancelled',
            'instructions' => 'string|max:1000',
            'side_effects_warning' => 'string|max:1000',
        ]);

        $prescription->update($validated);

        return response()->json([
            'message' => 'Prescription updated successfully',
            'prescription' => $prescription,
        ]);
    }

    /**
     * Get active prescriptions for a patient
     */
    public function getActive(Request $request)
    {
        $prescriptions = Prescription::where('patient_id', $request->user()->id)
            ->active()
            ->get();

        return response()->json($prescriptions);
    }
}
