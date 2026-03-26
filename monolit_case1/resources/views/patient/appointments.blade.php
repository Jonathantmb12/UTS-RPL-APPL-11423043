@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 d-flex align-items-center gap-2">
                <i class="bi bi-calendar-event"></i>
                Appointment Saya
            </h1>
            <p class="text-muted mb-0">Lihat dan kelola appointment Anda</p>
        </div>
    </div>

    @if($appointments->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i>
            Anda belum memiliki appointment. Hubungi rumah sakit untuk membuat jadwal.
        </div>
    @else
        <div class="row">
            @foreach($appointments as $appointment)
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <i class="bi bi-person-badge"></i>
                                        Dr. {{ $appointment->doctor->name ?? 'N/A' }}
                                    </h5>
                                    <small class="text-muted">{{ $appointment->doctor->specialization ?? 'General' }}</small>
                                </div>
                                <span class="badge 
                                    @if($appointment->status === 'scheduled') bg-warning
                                    @elseif($appointment->status === 'confirmed') bg-success
                                    @elseif($appointment->status === 'completed') bg-info
                                    @elseif($appointment->status === 'cancelled') bg-danger
                                    @endif
                                ">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $appointment->appointment_date->format('d M Y, H:i') }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $appointment->consultation_type }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-chat-dots"></i>
                                    {{ $appointment->reason }}
                                </small>
                            </div>

                            @if($appointment->notes)
                                <div class="alert alert-light mb-3">
                                    <small><strong>Catatan:</strong></small>
                                    <small class="d-block">{{ $appointment->notes }}</small>
                                </div>
                            @endif

                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
