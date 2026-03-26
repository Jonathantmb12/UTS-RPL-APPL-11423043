@extends('layouts.app')

@section('title', 'My Prescriptions')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 d-flex align-items-center gap-2">
                <i class="bi bi-capsule"></i>
                Resep Saya
            </h1>
            <p class="text-muted mb-0">Lihat resep yang diberikan oleh dokter</p>
        </div>
    </div>

    @if($prescriptions->isEmpty())
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i>
            Anda belum memiliki resep. Konsultasi dengan dokter untuk mendapatkan resep.
        </div>
    @else
        <div class="row">
            @foreach($prescriptions as $prescription)
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <i class="bi bi-capsule"></i>
                                        {{ $prescription->medication_name }}
                                    </h5>
                                    <small class="text-muted">{{ $prescription->dosage }}</small>
                                </div>
                                <span class="badge 
                                    @if(now() <= $prescription->expiry_date) bg-success
                                    @else bg-danger
                                    @endif
                                ">
                                    @if(now() <= $prescription->expiry_date)
                                        Aktif
                                    @else
                                        Expired
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    <i class="bi bi-person-badge"></i>
                                    Dr. {{ $prescription->doctor->name ?? 'N/A' }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar3"></i>
                                    Diberi: {{ $prescription->prescribed_date->format('d M Y') }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar3"></i>
                                    Berlaku sampai: {{ $prescription->expiry_date->format('d M Y') }}
                                </small>
                            </div>

                            <div class="alert alert-light mb-3">
                                <small class="d-block">
                                    <strong>Frekuensi:</strong> {{ $prescription->frequency }}
                                </small>
                                <small class="d-block">
                                    <strong>Jumlah:</strong> {{ $prescription->quantity }}
                                </small>
                            </div>

                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('prescriptions.show', $prescription->id) }}" class="btn btn-sm btn-outline-primary">
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
