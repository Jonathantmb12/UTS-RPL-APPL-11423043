@extends('layouts.app')

@section('title', 'My Health Record')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 d-flex align-items-center gap-2">
                <i class="bi bi-file-medical"></i>
                Riwayat Kesehatan Saya
            </h1>
            <p class="text-muted mb-0">Lihat catatan medis dan vital signs Anda</p>
        </div>
    </div>

    @if(!$healthRecord)
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i>
            Belum ada riwayat kesehatan. Konsultasi dengan dokter untuk membuat riwayat kesehatan.
        </div>
    @else
        <!-- Vital Signs -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom px-4 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-heart-pulse"></i> Vital Signs
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-speedometer"></i> Blood Pressure
                                    </small>
                                    <h5 class="mb-0">
                                        {{ $healthRecord->blood_pressure_systolic ?? 'N/A' }}/{{ $healthRecord->blood_pressure_diastolic ?? 'N/A' }}
                                    </h5>
                                    <small class="text-muted">mmHg</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-heart"></i> Heart Rate
                                    </small>
                                    <h5 class="mb-0">{{ $healthRecord->heart_rate ?? 'N/A' }}</h5>
                                    <small class="text-muted">bpm</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-thermometer"></i> Temperature
                                    </small>
                                    <h5 class="mb-0">{{ $healthRecord->body_temperature_celsius ?? 'N/A' }}</h5>
                                    <small class="text-muted">°C</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-calendar3"></i> Last Checkup
                                    </small>
                                    <h5 class="mb-0">{{ $healthRecord->last_checkup->format('d M Y') ?? 'N/A' }}</h5>
                                    <small class="text-muted">Date</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical History -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom px-4 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clipboard-pulse"></i> Medical History
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            {{ $healthRecord->medical_history ?? 'Tidak ada informasi' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current Medications -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom px-4 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-capsule"></i> Current Medications
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            {{ $healthRecord->current_medications ?? 'Tidak ada informasi' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Allergies & Previous Surgeries -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom px-4 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Allergies
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            {{ $healthRecord->allergies ?? 'Tidak ada alergi' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Family History -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom px-4 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-diagram-3"></i> Family History
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            {{ $healthRecord->family_history ?? 'Tidak ada informasi' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previous Surgeries & Lifestyle -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom px-4 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-hospital"></i> Previous Surgeries
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            {{ $healthRecord->previous_surgeries ?? 'Tidak ada' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom px-4 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-activity"></i> Lifestyle Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            {{ $healthRecord->lifestyle_notes ?? 'Tidak ada informasi' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
