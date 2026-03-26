<?php

namespace App\Http\Controllers;

use App\Models\PatientOutcome;
use App\Models\DoctorPerformanceMetric;
use App\Models\DrugUsageAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get patient outcomes
     */
    public function patientOutcomes(Request $request)
    {
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'doctor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = PatientOutcome::query();

        if ($request->user()->role === 'doctor') {
            $query->where('doctor_id', $request->user()->id);
        }

        if ($request->patient_id) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->recovery_status) {
            $query->where('recovery_status', $request->recovery_status);
        }

        $outcomes = $query->with(['patient', 'doctor'])
            ->orderBy('recorded_date', 'desc')
            ->paginate(20);

        return response()->json($outcomes);
    }

    /**
     * Record patient outcome
     */
    public function recordOutcome(Request $request)
    {
        if ($request->user()->role !== 'doctor') {
            return response()->json(['message' => 'Only doctors can record outcomes'], 403);
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'outcome_summary' => 'required|string',
            'recovery_status' => 'in:excellent,good,fair,poor',
            'follow_up_appointments' => 'integer|min:0',
            'satisfaction_score' => 'numeric|min:1|max:5',
            'symptoms_progression' => 'array',
        ]);

        $outcome = PatientOutcome::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $request->user()->id,
            'outcome_summary' => $validated['outcome_summary'],
            'recovery_status' => $validated['recovery_status'] ?? null,
            'follow_up_appointments' => $validated['follow_up_appointments'] ?? 0,
            'satisfaction_score' => $validated['satisfaction_score'] ?? null,
            'symptoms_progression' => $validated['symptoms_progression'] ?? null,
            'recorded_date' => now(),
        ]);

        return response()->json([
            'message' => 'Patient outcome recorded successfully',
            'outcome' => $outcome,
        ], 201);
    }

    /**
     * Get doctor performance metrics
     */
    public function doctorMetrics(Request $request)
    {
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'doctor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = DoctorPerformanceMetric::query();

        if ($request->user()->role === 'doctor') {
            $query->where('doctor_id', $request->user()->id);
        }

        if ($request->doctor_id && $request->user()->role === 'admin') {
            $query->where('doctor_id', $request->doctor_id);
        }

        $metrics = $query->with('doctor')->paginate(20);

        return response()->json($metrics);
    }

    /**
     * Update doctor performance metrics
     */
    public function updateDoctorMetrics($doctorId)
    {
        $appointmentStats = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
            ')
            ->first();

        $patientCount = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->distinct('patient_id')
            ->count('patient_id');

        $metrics = DoctorPerformanceMetric::updateOrCreate(
            ['doctor_id' => $doctorId],
            [
                'total_appointments' => $appointmentStats->total ?? 0,
                'completed_appointments' => $appointmentStats->completed ?? 0,
                'cancelled_appointments' => $appointmentStats->cancelled ?? 0,
                'patient_count' => $patientCount,
                'last_updated' => now(),
            ]
        );

        return $metrics;
    }

    /**
     * Get drug usage analytics
     */
    public function drugUsage(Request $request)
    {
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'pharmacist') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = DrugUsageAnalytic::query();

        if ($request->medication_name) {
            $query->where('medication_name', 'like', '%' . $request->medication_name . '%');
        }

        $analytics = $query->orderBy('total_prescribed', 'desc')
            ->paginate(20);

        return response()->json($analytics);
    }

    /**
     * Get dashboard overview for admin
     */
    public function dashboard(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $overview = [
            'total_users' => DB::table('users')->count(),
            'total_patients' => DB::table('users')->where('role', 'patient')->count(),
            'total_doctors' => DB::table('users')->where('role', 'doctor')->count(),
            'total_pharmacists' => DB::table('users')->where('role', 'pharmacist')->count(),
            'total_appointments' => DB::table('appointments')->count(),
            'completed_appointments' => DB::table('appointments')->where('status', 'completed')->count(),
            'total_prescriptions' => DB::table('prescriptions')->count(),
            'active_prescriptions' => DB::table('prescriptions')->where('status', 'active')->count(),
            'total_payments' => DB::table('payments')->sum('amount'),
            'completed_payments' => DB::table('payments')->where('status', 'completed')->sum('amount'),
            'total_insurance_claims' => DB::table('insurance_claims')->count(),
            'approved_claims' => DB::table('insurance_claims')->where('status', 'approved')->count(),
        ];

        return response()->json($overview);
    }
}
