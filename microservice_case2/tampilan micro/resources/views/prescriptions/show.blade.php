@extends('layouts.app')

@section('title', 'Prescription Details')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 d-flex align-items-center gap-2">
                        <i class="bi bi-capsule"></i>
                        {{ $prescription->medication_name }}
                    </h1>
                    <p class="text-muted mb-0">
                        <span class="badge bg-info me-2">{{ $prescription->dosage }} {{ $prescription->frequency }}</span>
                        @if(\Carbon\Carbon::parse($prescription->expiration_date) < now())
                            <span class="badge bg-danger">EXPIRED</span>
                        @elseif(\Carbon\Carbon::parse($prescription->expiration_date)->diffInDays(now()) <= 7)
                            <span class="badge bg-warning">EXPIRING SOON</span>
                        @else
                            <span class="badge bg-success">ACTIVE</span>
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('prescriptions.edit', $prescription->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('prescriptions.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Prescription Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Prescription Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Medication Name</label>
                            <p class="h6 mb-0">{{ $prescription->medication_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Dosage</label>
                            <p class="h6 mb-0">{{ $prescription->dosage }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Frequency</label>
                            <p class="h6 mb-0">{{ $prescription->frequency }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Quantity</label>
                            <p class="h6 mb-0">{{ $prescription->quantity }} units</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Prescribed Date</label>
                            <p class="h6 mb-0">{{ \Carbon\Carbon::parse($prescription->prescribed_date)->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Expiration Date</label>
                            <p class="h6 mb-0">
                                {{ \Carbon\Carbon::parse($prescription->expiration_date)->format('M d, Y') }}
                                @if(\Carbon\Carbon::parse($prescription->expiration_date) < now())
                                    <span class="badge bg-danger ms-2">EXPIRED</span>
                                @endif
                            </p>
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
                                <strong>{{ $prescription->patient->name }}</strong>
                            </p>
                            <p class="text-muted small mb-0">{{ $prescription->patient->email }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Phone</label>
                            <p class="h6 mb-0">{{ $prescription->patient->phone_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Date of Birth</label>
                            <p class="h6 mb-0">{{ \Carbon\Carbon::parse($prescription->patient->date_of_birth)->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Blood Type</label>
                            <p class="h6 mb-0">
                                <span class="badge bg-danger">{{ $prescription->patient->blood_type }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Address</label>
                            <p class="h6 mb-0">{{ $prescription->patient->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor Information -->
            <div class="card border-0 shadow-sm">
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
                                <strong>{{ $prescription->doctor->name }}</strong>
                            </p>
                            <p class="text-muted small mb-0">
                                <span class="badge bg-info">{{ $prescription->doctor->specialization }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Email</label>
                            <p class="h6 mb-0">{{ $prescription->doctor->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Phone</label>
                            <p class="h6 mb-0">{{ $prescription->doctor->phone_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">License</label>
                            <p class="h6 mb-0">{{ $prescription->doctor->license_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Hospital</label>
                            <p class="h6 mb-0">{{ $prescription->doctor->hospital_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
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
                            $isExpired = \Carbon\Carbon::parse($prescription->expiration_date) < now();
                            $isSoonExpired = \Carbon\Carbon::parse($prescription->expiration_date)->diffInDays(now()) <= 7;
                        @endphp
                        @if($prescription->status == 'active' && !$isExpired)
                            <span class="badge bg-success p-2">Active</span>
                        @elseif($isExpired)
                            <span class="badge bg-danger p-2">Expired</span>
                        @else
                            <span class="badge bg-warning p-2">{{ ucfirst($prescription->status) }}</span>
                        @endif
                    </p>
                    <p class="text-muted small">
                        @if($isExpired)
                            This prescription has expired and should not be used
                        @elseif($isSoonExpired)
                            This prescription will expire soon
                        @else
                            This prescription is currently active
                        @endif
                    </p>
                </div>
            </div>

            <!-- Instructions -->
            @if($prescription->special_instructions)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">
                        <i class="bi bi-clipboard-check me-2"></i>Special Instructions
                    </h6>
                    <p class="small mb-0">{{ $prescription->special_instructions }}</p>
                </div>
            </div>
            @endif

            <!-- Duration Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">
                        <i class="bi bi-calendar-event me-2"></i>Duration
                    </h6>
                    <p class="small mb-0">
                        <strong>{{ $prescription->duration_days }}</strong> days from prescribed date
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">Actions</h6>
                    <div class="d-flex flex-column gap-2">
                        <form action="{{ route('prescriptions.destroy', $prescription->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                                <i class="bi bi-trash me-2"></i>Delete Prescription
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
