<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\ElectronicHealthRecord;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display a listing of patients (Web & API)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            
            $query = User::where('role', 'patient');
            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            }
            
            $patients = $query->paginate($perPage);
            
            if ($request->expectsJson()) {
                return response()->json($patients);
            }
            
            return view('patients.index', ['patients' => $patients]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show create patient form (Web only)
     */
    public function create(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Use POST /api/patients'], 405);
        }
        return view('patients.form');
    }

    /**
     * Store a newly created patient in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone_number' => 'required|unique:users,phone_number',
            'address' => 'required|string',
            'blood_type' => 'required|in:A,B,AB,O',
            'allergies' => 'nullable|string',
        ]);

        try {
            $patient = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'patient',
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'blood_type' => $validated['blood_type'],
                'allergies' => $validated['allergies'] ?? null,
                'is_verified' => true,
                'is_active' => true,
            ]);

            // Create electronic health record
            ElectronicHealthRecord::create([
                'patient_id' => $patient->id,
                'doctor_id' => null,
                'medical_history' => '',
                'current_medications' => '',
                'allergies' => $validated['allergies'] ?? '',
                'last_checkup' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Patient created successfully', 'patient' => $patient], 201);
            }

            return redirect()->route('patients.index')->with('success', 'Patient created successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified patient
     */
    public function show($id, Request $request)
    {
        try {
            $patient = User::where('id', $id)->where('role', 'patient')->firstOrFail();
            
            if ($request->expectsJson()) {
                return response()->json($patient);
            }
            
            return view('patients.show', ['patient' => $patient]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Patient not found'], 404);
            }
            return back()->withErrors(['error' => 'Patient not found']);
        }
    }

    /**
     * Show edit patient form (Web only)
     */
    public function edit($id, Request $request)
    {
        try {
            $patient = User::where('id', $id)->where('role', 'patient')->firstOrFail();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Use PUT /api/patients/{id}'], 405);
            }
            
            return view('patients.form', ['patient' => $patient]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Patient not found']);
        }
    }

    /**
     * Update the specified patient in database
     */
    public function update(Request $request, $id)
    {
        try {
            $patient = User::where('id', $id)->where('role', 'patient')->firstOrFail();

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'date_of_birth' => 'sometimes|date',
                'gender' => 'sometimes|in:male,female,other',
                'phone_number' => 'sometimes|unique:users,phone_number,' . $id,
                'address' => 'sometimes|string',
                'blood_type' => 'sometimes|in:A,B,AB,O',
                'allergies' => 'nullable|string',
            ]);

            $patient->update($validated);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Patient updated successfully', 'patient' => $patient]);
            }

            return redirect()->route('patients.show', $id)->with('success', 'Patient updated successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified patient from database
     */
    public function destroy($id, Request $request)
    {
        try {
            $patient = User::where('id', $id)->where('role', 'patient')->firstOrFail();
            $patient->delete();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Patient deleted successfully']);
            }

            return redirect()->route('patients.index')->with('success', 'Patient deleted successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get patient appointments
     */
    public function getAppointments($id, Request $request)
    {
        try {
            $patient = User::where('id', $id)->where('role', 'patient')->firstOrFail();
            $appointments = Appointment::where('patient_id', $id)->get();

            if ($request->expectsJson()) {
                return response()->json($appointments);
            }

            return view('patients.appointments', ['patient' => $patient, 'appointments' => $appointments]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Patient not found'], 404);
            }
            return back()->withErrors(['error' => 'Patient not found']);
        }
    }

    /**
     * Get patient prescriptions
     */
    public function getPrescriptions($id, Request $request)
    {
        try {
            $patient = User::where('id', $id)->where('role', 'patient')->firstOrFail();
            $prescriptions = Prescription::where('patient_id', $id)->get();

            if ($request->expectsJson()) {
                return response()->json($prescriptions);
            }

            return view('patients.prescriptions', ['patient' => $patient, 'prescriptions' => $prescriptions]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Patient not found'], 404);
            }
            return back()->withErrors(['error' => 'Patient not found']);
        }
    }

    /**
     * Get patient health record
     */
    public function getHealthRecord($id, Request $request)
    {
        try {
            $patient = User::where('id', $id)->where('role', 'patient')->firstOrFail();
            $healthRecord = ElectronicHealthRecord::where('patient_id', $id)->latest()->first();

            if ($request->expectsJson()) {
                return response()->json($healthRecord);
            }

            return view('patients.health-record', ['patient' => $patient, 'healthRecord' => $healthRecord]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Health record not found'], 404);
            }
            return back()->withErrors(['error' => 'Health record not found']);
        }
    }

    /**
     * Update patient health record
     */
    public function updateHealthRecord(Request $request, $id)
    {
        try {
            $patient = User::where('id', $id)->where('role', 'patient')->firstOrFail();

            $validated = $request->validate([
                'medical_history' => 'sometimes|string',
                'current_medications' => 'sometimes|string',
                'allergies' => 'sometimes|string',
            ]);

            $healthRecord = ElectronicHealthRecord::where('patient_id', $id)->latest()->first();
            
            if (!$healthRecord) {
                $healthRecord = ElectronicHealthRecord::create([
                    'patient_id' => $id,
                    'doctor_id' => null,
                    'medical_history' => $validated['medical_history'] ?? '',
                    'current_medications' => $validated['current_medications'] ?? '',
                    'allergies' => $validated['allergies'] ?? '',
                    'last_checkup' => now(),
                ]);
            } else {
                $healthRecord->update($validated);
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Health record updated', 'healthRecord' => $healthRecord]);
            }

            return back()->with('success', 'Health record updated successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Patient Portal: View my appointments
     */
    public function myAppointments(Request $request)
    {
        try {
            $patient = Auth::user();
            $appointments = Appointment::where('patient_id', $patient->id)
                ->with(['doctor'])
                ->orderBy('appointment_date', 'desc')
                ->get();

            if ($request->expectsJson()) {
                return response()->json($appointments);
            }

            return view('patient.appointments', ['appointments' => $appointments]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Patient Portal: View my prescriptions
     */
    public function myPrescriptions(Request $request)
    {
        try {
            $patient = Auth::user();
            $prescriptions = Prescription::where('patient_id', $patient->id)
                ->with(['doctor'])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($request->expectsJson()) {
                return response()->json($prescriptions);
            }

            return view('patient.prescriptions', ['prescriptions' => $prescriptions]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Patient Portal: View my health record
     */
    public function myHealthRecord(Request $request)
    {
        try {
            $patient = Auth::user();
            $healthRecord = ElectronicHealthRecord::where('patient_id', $patient->id)
                ->latest()
                ->first();

            if ($request->expectsJson()) {
                return response()->json($healthRecord);
            }

            return view('patient.health-record', ['healthRecord' => $healthRecord, 'patient' => $patient]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
