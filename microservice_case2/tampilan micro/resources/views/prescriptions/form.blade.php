@extends('layouts.app')

@section('title', isset($prescription) ? 'Edit Prescription' : 'Create Prescription')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i>
                {{ isset($prescription) ? 'Edit Prescription' : 'Create New Prescription' }}
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ isset($prescription) ? route('prescriptions.update', $prescription->id) : route('prescriptions.store') }}" method="POST">
                        @csrf
                        @if(isset($prescription)) @method('PUT') @endif

                        <!-- Patient Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Patient</label>
                            <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                                <option value="">-- Select a Patient --</option>
                                @foreach(\App\Models\User::where('role', 'patient')->get() as $patient)
                                    <option value="{{ $patient->id }}" 
                                            {{ old('patient_id', $prescription->patient_id ?? '') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }} ({{ $patient->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Doctor Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Doctor</label>
                            <select name="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                                <option value="">-- Select a Doctor --</option>
                                @foreach(\App\Models\User::where('role', 'doctor')->get() as $doctor)
                                    <option value="{{ $doctor->id }}" 
                                            {{ old('doctor_id', $prescription->doctor_id ?? '') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->name }} - {{ $doctor->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Medication Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Medication Name</label>
                            <input type="text" name="medication_name" class="form-control @error('medication_name') is-invalid @enderror"
                                   value="{{ old('medication_name', $prescription->medication_name ?? '') }}" required>
                            @error('medication_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dosage -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dosage</label>
                            <input type="text" name="dosage" class="form-control @error('dosage') is-invalid @enderror"
                                   placeholder="e.g., 500mg" value="{{ old('dosage', $prescription->dosage ?? '') }}" required>
                            @error('dosage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Frequency -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Frequency</label>
                            <select name="frequency" class="form-select @error('frequency') is-invalid @enderror" required>
                                <option value="">-- Select Frequency --</option>
                                <option value="Once daily" {{ old('frequency', $prescription->frequency ?? '') == 'Once daily' ? 'selected' : '' }}>Once daily</option>
                                <option value="Twice daily" {{ old('frequency', $prescription->frequency ?? '') == 'Twice daily' ? 'selected' : '' }}>Twice daily</option>
                                <option value="Three times daily" {{ old('frequency', $prescription->frequency ?? '') == 'Three times daily' ? 'selected' : '' }}>Three times daily</option>
                                <option value="Every 4 hours" {{ old('frequency', $prescription->frequency ?? '') == 'Every 4 hours' ? 'selected' : '' }}>Every 4 hours</option>
                                <option value="Every 6 hours" {{ old('frequency', $prescription->frequency ?? '') == 'Every 6 hours' ? 'selected' : '' }}>Every 6 hours</option>
                                <option value="Every 8 hours" {{ old('frequency', $prescription->frequency ?? '') == 'Every 8 hours' ? 'selected' : '' }}>Every 8 hours</option>
                                <option value="As needed" {{ old('frequency', $prescription->frequency ?? '') == 'As needed' ? 'selected' : '' }}>As needed</option>
                            </select>
                            @error('frequency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Quantity</label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', $prescription->quantity ?? 10) }}" min="1" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration (Days) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Duration (Days)</label>
                            <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror"
                                   value="{{ old('duration_days', $prescription->duration_days ?? 7) }}" min="1" required>
                            @error('duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Prescription will expire after this many days</small>
                        </div>

                        <!-- Special Instructions -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Special Instructions</label>
                            <textarea name="special_instructions" class="form-control @error('special_instructions') is-invalid @enderror" rows="3" placeholder="e.g., Take with food, avoid dairy">{{ old('special_instructions', $prescription->special_instructions ?? '') }}</textarea>
                            @error('special_instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        @if(isset($prescription))
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', $prescription->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ old('status', $prescription->status ?? '') == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="cancelled" {{ old('status', $prescription->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>{{ isset($prescription) ? 'Update' : 'Create' }} Prescription
                            </button>
                            <a href="{{ route('prescriptions.index') }}" class="btn btn-light px-4">
                                <i class="bi bi-x-lg me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">
                        <i class="bi bi-info-circle me-2"></i>Guidelines
                    </h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2">
                            <strong>Patient:</strong> Select the patient receiving the medication
                        </li>
                        <li class="mb-2">
                            <strong>Doctor:</strong> Select the prescribing physician
                        </li>
                        <li class="mb-2">
                            <strong>Medication:</strong> Enter the exact medication name
                        </li>
                        <li class="mb-2">
                            <strong>Dosage:</strong> Include dose and unit (e.g., 500mg)
                        </li>
                        <li class="mb-2">
                            <strong>Frequency:</strong> How often the medication should be taken
                        </li>
                        <li class="mb-2">
                            <strong>Duration:</strong> Length prescription is valid for
                        </li>
                        <li class="mb-2">
                            <strong>Instructions:</strong> Any special usage notes or warnings
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
