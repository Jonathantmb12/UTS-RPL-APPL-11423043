<?php

namespace App\Http\Controllers;

use App\Models\ElectronicHealthRecord;
use Illuminate\Http\Request;

class EHRController extends Controller
{
    /**
     * Get patient's health record
     */
    public function show(Request $request)
    {
        $patientId = $request->user()->role === 'patient' ? $request->user()->id : $request->patient_id;

        $ehr = ElectronicHealthRecord::where('patient_id', $patientId)->first();

        if (!$ehr) {
            return response()->json(['message' => 'Health record not found'], 404);
        }

        return response()->json($ehr);
    }

    /**
     * Create or update health record
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'doctor') {
            return response()->json(['message' => 'Only doctors can create health records'], 403);
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'medical_history' => 'string',
            'current_medications' => 'string',
            'allergies' => 'string',
            'previous_surgeries' => 'string',
            'family_history' => 'string',
            'lifestyle_notes' => 'string',
            'blood_type' => 'in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'height_cm' => 'numeric|min:50|max:250',
            'weight_kg' => 'numeric|min:1|max:300',
            'blood_pressure_systolic' => 'integer',
            'blood_pressure_diastolic' => 'integer',
            'heart_rate' => 'numeric',
            'body_temperature_celsius' => 'numeric',
        ]);

        $ehr = ElectronicHealthRecord::updateOrCreate(
            ['patient_id' => $validated['patient_id']],
            array_merge($validated, ['doctor_id' => $request->user()->id])
        );

        return response()->json([
            'message' => 'Health record ' . ($ehr->wasRecentlyCreated ? 'created' : 'updated') . ' successfully',
            'ehr' => $ehr,
        ], $ehr->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Update vitals
     */
    public function updateVitals(Request $request)
    {
        if ($request->user()->role !== 'doctor' && $request->user()->role !== 'patient') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $patientId = $request->user()->role === 'patient' ? $request->user()->id : $request->patient_id;

        $validated = $request->validate([
            'blood_pressure_systolic' => 'integer',
            'blood_pressure_diastolic' => 'integer',
            'heart_rate' => 'numeric',
            'body_temperature_celsius' => 'numeric',
            'weight_kg' => 'numeric',
        ]);

        $ehr = ElectronicHealthRecord::updateOrCreate(
            ['patient_id' => $patientId],
            $validated
        );

        return response()->json([
            'message' => 'Vitals updated successfully',
            'ehr' => $ehr,
        ]);
    }
}
