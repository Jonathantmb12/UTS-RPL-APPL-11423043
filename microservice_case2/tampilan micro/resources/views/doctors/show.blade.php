@extends('layouts.app')

@section('title', 'Doctor Profile')

@section('content')
<div class="container-fluid py-4">
    <!-- Header with Back Button -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge"></i>
                        {{ $doctor->name }}
                    </h1>
                    <p class="text-muted mb-0">
                        <span class="badge bg-info me-2">{{ $doctor->specialization }}</span>
                        <span class="text-muted small">{{ $doctor->email }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('doctors.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Name</label>
                            <p class="h6 mb-0">{{ $doctor->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Email</label>
                            <p class="h6 mb-0">
                                <a href="mailto:{{ $doctor->email }}">{{ $doctor->email }}</a>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Phone Number</label>
                            <p class="h6 mb-0">{{ $doctor->phone_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Status</label>
                            <p class="h6 mb-0">
                                @if($doctor->status == 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-briefcase me-2"></i>Professional Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Specialization</label>
                            <p class="h6 mb-0">
                                <span class="badge bg-info">{{ $doctor->specialization }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block mb-1">Medical License Number</label>
                            <p class="h6 mb-0">{{ $doctor->license_number }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small d-block mb-1">Hospital/Clinic</label>
                            <p class="h6 mb-0">{{ $doctor->hospital_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-primary mb-3">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <p class="text-muted small mb-1">Total Appointments</p>
                            <h5 class="mb-0">{{ $appointments->count() }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-success mb-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <p class="text-muted small mb-1">Completed</p>
                            <h5 class="mb-0">{{ $appointments->where('status', 'completed')->count() }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-warning mb-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <p class="text-muted small mb-1">Scheduled</p>
                            <h5 class="mb-0">{{ $appointments->where('status', 'scheduled')->count() }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-info mb-3">
                                <i class="bi bi-capsule"></i>
                            </div>
                            <p class="text-muted small mb-1">Prescriptions</p>
                            <h5 class="mb-0">{{ $prescriptions->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Tab -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2"></i>Recent Appointments
                    </h5>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Patient</th>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments->take(5) as $appointment)
                                    <tr>
                                        <td>
                                            <strong>{{ $appointment->patient->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $appointment->patient->email }}</small>
                                        </td>
                                        <td>
                                            {{ $appointment->appointment_date->format('M d, Y H:i') }}
                                            <br>
                                            <small class="text-muted">{{ $appointment->duration_minutes }} mins</small>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: #667eea;">
                                                {{ ucfirst($appointment->consultation_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($appointment->status) {
                                                    'scheduled' => 'bg-warning',
                                                    'confirmed' => 'bg-info',
                                                    'completed' => 'bg-success',
                                                    'cancelled' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0 text-center py-4">No appointments found</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">
                        <i class="bi bi-info-circle me-2"></i>Quick Info
                    </h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <strong>Member Since:</strong>
                            <br>
                            {{ $doctor->created_at->format('M d, Y') }}
                        </li>
                        <li class="mb-2">
                            <strong>Total Patients:</strong>
                            <br>
                            {{ $appointments->groupBy('patient_id')->count() }}
                        </li>
                        <li class="mb-2">
                            <strong>Performance Rating:</strong>
                            <br>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">Actions</h6>
                    <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#messageModal">
                        <i class="bi bi-chat me-2"></i>Send Message
                    </button>
                    <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-2"></i>Delete Doctor
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Send Message to {{ $doctor->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="messageForm">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>
</div>

<style>
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
    margin-right: auto;
    color: white;
    font-size: 24px;
}
</style>
@endsection
