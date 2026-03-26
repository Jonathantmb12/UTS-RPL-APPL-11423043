@extends('layouts.app')

@section('title', 'Appointment')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6"><i class="bi bi-calendar-check"></i> Appointment</h1>
            <p class="text-muted">Kelola jadwal appointment pasien</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                <i class="bi bi-plus-lg"></i> Buat Appointment
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-label">Total Appointment</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['scheduled'] ?? 0 }}</div>
                <div class="stat-label">Terjadwal</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['completed'] ?? 0 }}</div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['cancelled'] ?? 0 }}</div>
                <div class="stat-label">Dibatalkan</div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('appointments.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="scheduled" @if(request('status') === 'scheduled') selected @endif>Terjadwal</option>
                        <option value="confirmed" @if(request('status') === 'confirmed') selected @endif>Dikonfirmasi</option>
                        <option value="completed" @if(request('status') === 'completed') selected @endif>Selesai</option>
                        <option value="cancelled" @if(request('status') === 'cancelled') selected @endif>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Appointment</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Tanggal & Jam</th>
                        <th>Tipe</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                        <tr>
                            <td>
                                <strong>{{ $appt->patient->name }}</strong><br>
                                <small class="text-muted">{{ $appt->patient->email }}</small>
                            </td>
                            <td>
                                <strong>{{ $appt->doctor->name }}</strong><br>
                                <small class="text-muted">{{ $appt->doctor->specialization }}</small>
                            </td>
                            <td>
                                <strong>{{ $appt->appointment_date->format('d M Y') }}</strong><br>
                                <small>{{ $appt->appointment_date->format('H:i') }} ({{ $appt->duration_minutes }} min)</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $appt->consultation_type }}</span>
                            </td>
                            <td>{{ Str::limit($appt->reason_for_visit, 30) }}</td>
                            <td>
                                <span class="badge-status badge-{{ $appt->status }}">{{ ucfirst($appt->status) }}</span>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info" title="Lihat" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal{{ $appt->id }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($appt->status === 'scheduled')
                                    <button type="button" class="btn btn-sm btn-success" title="Konfirmasi" onclick="confirmAppointment({{ $appt->id }})">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                @endif
                                @if($appt->status !== 'completed' && $appt->status !== 'cancelled')
                                    <button type="button" class="btn btn-sm btn-danger" title="Batalkan" onclick="cancelAppointment({{ $appt->id }})">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada appointment</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $appointments->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Create Appointment -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Appointment Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('appointments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pasien</label>
                        <select class="form-select" name="patient_id" required>
                            <option value="">Pilih Pasien</option>
                            {{-- Populate with patients --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dokter</label>
                        <select class="form-select" name="doctor_id" required>
                            <option value="">Pilih Dokter</option>
                            {{-- Populate with doctors --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal & Jam</label>
                        <input type="datetime-local" class="form-control" name="appointment_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Kunjungan</label>
                        <textarea class="form-control" name="reason_for_visit" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmAppointment(id) {
    if(confirm('Konfirmasi appointment ini?')) {
        fetch(`/appointments/${id}/confirm`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        }).then(() => location.reload());
    }
}

function cancelAppointment(id) {
    if(confirm('Batalkan appointment ini?')) {
        fetch(`/appointments/${id}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: 'Dibatalkan oleh admin' })
        }).then(() => location.reload());
    }
}
</script>
@endsection
