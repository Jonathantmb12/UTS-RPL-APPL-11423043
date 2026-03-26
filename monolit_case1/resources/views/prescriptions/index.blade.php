@extends('layouts.app')

@section('title', 'Manajemen Resep')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6"><i class="bi bi-capsule"></i> Manajemen Resep</h1>
            <p class="text-muted">Kelola resep pasien</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPrescriptionModal">
                <i class="bi bi-plus-lg"></i> Buat Resep
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-label">Total Resep</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['active'] ?? 0 }}</div>
                <div class="stat-label">Aktif</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['expired'] ?? 0 }}</div>
                <div class="stat-label">Expired</div>
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
            <form action="{{ route('prescriptions.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama obat..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" @if(request('status') === 'active') selected @endif>Aktif</option>
                        <option value="inactive" @if(request('status') === 'inactive') selected @endif>Tidak Aktif</option>
                        <option value="expired" @if(request('status') === 'expired') selected @endif>Expired</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Prescriptions Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Resep</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Pasien</th>
                        <th>Obat</th>
                        <th>Dosage</th>
                        <th>Frekuensi</th>
                        <th>Dokter</th>
                        <th>Tanggal Resep</th>
                        <th>Expired</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $rx)
                        <tr>
                            <td>
                                <strong>{{ $rx->patient->name }}</strong><br>
                                <small class="text-muted">{{ $rx->patient->email }}</small>
                            </td>
                            <td>{{ $rx->medication_name }}</td>
                            <td>{{ $rx->dosage }}</td>
                            <td>{{ $rx->frequency }}</td>
                            <td>{{ $rx->doctor->name }}</td>
                            <td>{{ $rx->prescribed_date->format('d M Y') }}</td>
                            <td>
                                @if($rx->expiration_date)
                                    <strong>{{ $rx->expiration_date->format('d M Y') }}</strong>
                                    @if($rx->expiration_date < now())
                                        <span class="badge bg-danger">EXPIRED</span>
                                    @elseif($rx->expiration_date < now()->addDays(7))
                                        <span class="badge bg-warning">SOON</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge-status badge-{{ $rx->status }}">{{ ucfirst($rx->status) }}</span>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewPrescriptionModal{{ $rx->id }}" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPrescriptionModal{{ $rx->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" onclick="deletePrescription({{ $rx->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Tidak ada resep</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $prescriptions->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Create Prescription -->
<div class="modal fade" id="createPrescriptionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Resep Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('prescriptions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pasien</label>
                            <select class="form-select" name="patient_id" required>
                                <option value="">Pilih Pasien</option>
                                {{-- Populate with patients --}}
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dokter</label>
                            <select class="form-select" name="doctor_id" required>
                                <option value="">Pilih Dokter</option>
                                {{-- Populate with doctors --}}
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" class="form-control" name="medication_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dosage</label>
                            <input type="text" class="form-control" name="dosage" placeholder="Cth: 500mg" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frekuensi</label>
                            <input type="text" class="form-control" name="frequency" placeholder="Cth: 3x sehari" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kuantitas</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durasi (hari)</label>
                        <input type="number" class="form-control" name="duration_days" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instruksi</label>
                        <textarea class="form-control" name="instructions" rows="2" placeholder="Cth: Minum dengan makanan"></textarea>
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
function deletePrescription(id) {
    if(confirm('Hapus resep ini?')) {
        fetch(`/prescriptions/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        }).then(() => location.reload());
    }
}
</script>
@endsection
