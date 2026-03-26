@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-check"></i>
                        Appointment Details
                    </h1>
                    <p class="text-muted mb-0">
                        <span class="badge bg-info me-2">{{ ucfirst($appointment->consultation_type) }}</span>
                        <span class="badge {{ $appointment->status == 'completed' ? 'bg-success' : ($appointment->status == 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('appointments.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Appointment Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock me-2"></i>Appointment Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Date & Time</label>
                            <p class="h6 mb-0">{{ $appointment->appointment_date->format('l, M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Duration</label>
                            <p class="h6 mb-0">{{ $appointment->duration_minutes }} minutes</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Consultation Type</label>
                            <p class="h6 mb-0">
                                <span class="badge bg-info">{{ ucfirst($appointment->consultation_type) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Appointment Reason</label>
                            <p class="h6 mb-0">{{ $appointment->appointment_reason ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Patient Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0">
                                <strong>{{ $appointment->patient->name }}</strong>
                            </p>
                            <p class="text-muted small mb-0">{{ $appointment->patient->email }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Phone</label>
                            <p class="h6 mb-0">{{ $appointment->patient->phone_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Blood Type</label>
                            <p class="h6 mb-0">
                                <span class="badge bg-danger">{{ $appointment->patient->blood_type }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Age</label>
                            <p class="h6 mb-0">{{ \Carbon\Carbon::parse($appointment->patient->date_of_birth)->age }} years old</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Address</label>
                            <p class="h6 mb-0">{{ $appointment->patient->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-badge me-2"></i>Doctor Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="ms-3">
                            <p class="mb-0">
                                <strong>{{ $appointment->doctor->name }}</strong>
                            </p>
                            <p class="text-muted small mb-0">
                                <span class="badge bg-info">{{ $appointment->doctor->specialization }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Email</label>
                            <p class="h6 mb-0">{{ $appointment->doctor->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Phone</label>
                            <p class="h6 mb-0">{{ $appointment->doctor->phone_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Hospital</label>
                            <p class="h6 mb-0">{{ $appointment->doctor->hospital_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">License</label>
                            <p class="h6 mb-0">{{ $appointment->doctor->license_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Notes -->
            @if($appointment->notes)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat me-2"></i>Doctor Notes
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $appointment->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">
                        <i class="bi bi-flag me-2"></i>Status
                    </h6>
                    <p class="mb-3">
                        @php
                            $statusClass = match($appointment->status) {
                                'scheduled' => 'bg-warning',
                                'confirmed' => 'bg-info',
                                'completed' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            $statusText = match($appointment->status) {
                                'scheduled' => 'This appointment is scheduled',
                                'confirmed' => 'Patient has confirmed this appointment',
                                'completed' => 'Appointment has been completed',
                                'cancelled' => 'Appointment has been cancelled',
                                default => 'Unknown status'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} p-2">{{ ucfirst($appointment->status) }}</span>
                    </p>
                    <p class="text-muted small">{{ $statusText }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">Actions</h6>
                    <div class="d-flex flex-column gap-2">
                        @if($appointment->status == 'scheduled')
                            <form action="{{ route('appointments.confirm', $appointment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 btn-sm">
                                    <i class="bi bi-check-circle me-2"></i>Confirm
                                </button>
                            </form>
                            <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100 btn-sm">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </button>
                            </form>
                        @elseif($appointment->status == 'confirmed')
                            <form action="{{ route('appointments.complete', $appointment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info w-100 btn-sm" data-bs-toggle="modal" data-bs-target="#completeModal">
                                    <i class="bi bi-check me-2"></i>Mark Complete
                                </button>
                            </form>
                            <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100 btn-sm">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">
                        <i class="bi bi-clock-history me-2"></i>Timeline
                    </h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <strong>Created:</strong>
                            <br>
                            {{ $appointment->created_at->format('M d, Y H:i') }}
                        </li>
                        <li class="mb-2">
                            <strong>Last Updated:</strong>
                            <br>
                            {{ $appointment->updated_at->format('M d, Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Complete Appointment Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Mark Appointment as Complete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('appointments.complete', $appointment->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Doctor Notes</label>
                        <textarea class="form-control" name="notes" rows="4" placeholder="Enter any notes about the appointment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Complete Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
