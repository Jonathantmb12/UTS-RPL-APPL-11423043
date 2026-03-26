@extends('layouts.app')

@section('title', isset($appointment) ? 'Edit Appointment' : 'Create Appointment')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-flex align-items-center gap-2">
                <i class="bi bi-calendar-plus"></i>
                {{ isset($appointment) ? 'Edit Appointment' : 'Create New Appointment' }}
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ isset($appointment) ? route('appointments.update', $appointment->id) : route('appointments.store') }}" method="POST">
                        @csrf
                        @if(isset($appointment)) @method('PUT') @endif

                        <!-- Patient Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Patient</label>
                            <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required id="patientSelect">
                                <option value="">-- Select a Patient --</option>
                                @foreach(\App\Models\User::where('role', 'patient')->get() as $patient)
                                    <option value="{{ $patient->id }}" 
                                            {{ old('patient_id', $appointment->patient_id ?? '') == $patient->id ? 'selected' : '' }}>
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
                                            {{ old('doctor_id', $appointment->doctor_id ?? '') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->name }} - {{ $doctor->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date & Time -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Appointment Date & Time</label>
                            <input type="datetime-local" name="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror"
                                   value="{{ old('appointment_date', isset($appointment) ? $appointment->appointment_date->format('Y-m-d\TH:i') : '') }}" required>
                            @error('appointment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror"
                                   value="{{ old('duration_minutes', $appointment->duration_minutes ?? 30) }}" min="15" max="180" required>
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Consultation Type -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Consultation Type</label>
                            <select name="consultation_type" class="form-select @error('consultation_type') is-invalid @enderror" required>
                                <option value="">-- Select Type --</option>
                                <option value="in-person" {{ old('consultation_type', $appointment->consultation_type ?? '') == 'in-person' ? 'selected' : '' }}>In-Person</option>
                                <option value="video" {{ old('consultation_type', $appointment->consultation_type ?? '') == 'video' ? 'selected' : '' }}>Video Call</option>
                                <option value="phone" {{ old('consultation_type', $appointment->consultation_type ?? '') == 'phone' ? 'selected' : '' }}>Phone Call</option>
                            </select>
                            @error('consultation_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Appointment Reason -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Appointment Reason</label>
                            <textarea name="appointment_reason" class="form-control @error('appointment_reason') is-invalid @enderror" rows="3">{{ old('appointment_reason', $appointment->appointment_reason ?? '') }}</textarea>
                            @error('appointment_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        @if(isset($appointment))
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="scheduled" {{ old('status', $appointment->status ?? '') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="confirmed" {{ old('status', $appointment->status ?? '') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ old('status', $appointment->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $appointment->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>{{ isset($appointment) ? 'Update' : 'Create' }} Appointment
                            </button>
                            <a href="{{ route('appointments.index') }}" class="btn btn-light px-4">
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
                        <i class="bi bi-info-circle me-2"></i>Tips
                    </h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2">
                            <strong>Patient:</strong> Select the patient who will have the appointment
                        </li>
                        <li class="mb-2">
                            <strong>Doctor:</strong> Select the doctor providing consultation
                        </li>
                        <li class="mb-2">
                            <strong>Date & Time:</strong> Ensure there are no conflicts with other appointments
                        </li>
                        <li class="mb-2">
                            <strong>Duration:</strong> Typical appointment is 30 minutes
                        </li>
                        <li class="mb-2">
                            <strong>Type:</strong> Choose between in-person, video, or phone consultation
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
