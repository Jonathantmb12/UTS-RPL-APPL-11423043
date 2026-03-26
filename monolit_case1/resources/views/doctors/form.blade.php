@extends('layouts.app')

@section('title', isset($doctor) ? 'Edit Doctor' : 'Create New Doctor')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-flex align-items-center gap-2">
                <i class="bi bi-person-badge"></i>
                {{ isset($doctor) ? 'Edit Doctor' : 'Create New Doctor' }}
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ isset($doctor) ? route('doctors.update', $doctor->id) : route('doctors.store') }}" method="POST">
                        @csrf
                        @if(isset($doctor)) @method('PUT') @endif

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $doctor->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $doctor->email ?? '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password (only on create) -->
                        @if(!isset($doctor))
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Minimum 6 characters</small>
                            </div>
                        @endif

                        <!-- Phone -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="tel" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror"
                                   value="{{ old('phone_number', $doctor->phone_number ?? '') }}">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Specialization -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Specialization</label>
                            <select name="specialization" class="form-select @error('specialization') is-invalid @enderror" required>
                                <option value="">-- Select Specialization --</option>
                                <option value="Cardiology" {{ old('specialization', $doctor->specialization ?? '') == 'Cardiology' ? 'selected' : '' }}>Cardiology</option>
                                <option value="Pediatrics" {{ old('specialization', $doctor->specialization ?? '') == 'Pediatrics' ? 'selected' : '' }}>Pediatrics</option>
                                <option value="Orthopedics" {{ old('specialization', $doctor->specialization ?? '') == 'Orthopedics' ? 'selected' : '' }}>Orthopedics</option>
                                <option value="Neurology" {{ old('specialization', $doctor->specialization ?? '') == 'Neurology' ? 'selected' : '' }}>Neurology</option>
                                <option value="General Practice" {{ old('specialization', $doctor->specialization ?? '') == 'General Practice' ? 'selected' : '' }}>General Practice</option>
                                <option value="Dermatology" {{ old('specialization', $doctor->specialization ?? '') == 'Dermatology' ? 'selected' : '' }}>Dermatology</option>
                            </select>
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- License Number -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Medical License Number</label>
                            <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror"
                                   value="{{ old('license_number', $doctor->license_number ?? '') }}" required>
                            @error('license_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hospital/Clinic -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Hospital/Clinic Name</label>
                            <input type="text" name="hospital_name" class="form-control @error('hospital_name') is-invalid @enderror"
                                   value="{{ old('hospital_name', $doctor->hospital_name ?? '') }}">
                            @error('hospital_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="Aktif" {{ old('status', $doctor->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ old('status', $doctor->status ?? '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>{{ isset($doctor) ? 'Update' : 'Create' }} Doctor
                            </button>
                            <a href="{{ route('doctors.index') }}" class="btn btn-light px-4">
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
                        <i class="bi bi-info-circle me-2"></i>Information
                    </h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2">
                            <strong>Email:</strong> Used for login and communications
                        </li>
                        <li class="mb-2">
                            <strong>Specialization:</strong> Primary medical specialty
                        </li>
                        <li class="mb-2">
                            <strong>License Number:</strong> Medical registration number
                        </li>
                        <li class="mb-2">
                            <strong>Status:</strong> Active doctors can accept appointments
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
