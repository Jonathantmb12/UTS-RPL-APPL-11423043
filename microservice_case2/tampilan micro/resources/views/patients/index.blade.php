@extends('layouts.app')

@section('title', 'Kelola Pasien')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6"><i class="bi bi-people"></i> Kelola Pasien</h1>
            <p class="text-muted">Kelola data pasien dan informasi kesehatan mereka</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('patients.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Pasien
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('patients.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau nomor telepon..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="blood_type" class="form-select">
                        <option value="">Semua Golongan Darah</option>
                        <option value="O+" @if(request('blood_type') === 'O+') selected @endif>O+</option>
                        <option value="O-" @if(request('blood_type') === 'O-') selected @endif>O-</option>
                        <option value="A+" @if(request('blood_type') === 'A+') selected @endif>A+</option>
                        <option value="A-" @if(request('blood_type') === 'A-') selected @endif>A-</option>
                        <option value="B+" @if(request('blood_type') === 'B+') selected @endif>B+</option>
                        <option value="B-" @if(request('blood_type') === 'B-') selected @endif>B-</option>
                        <option value="AB+" @if(request('blood_type') === 'AB+') selected @endif>AB+</option>
                        <option value="AB-" @if(request('blood_type') === 'AB-') selected @endif>AB-</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Pasien</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Golongan Darah</th>
                        <th>Tanggal Lahir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td>
                                <strong>{{ $patient->name }}</strong>
                            </td>
                            <td>{{ $patient->email }}</td>
                            <td>{{ $patient->phone_number }}</td>
                            <td>
                                <span class="badge bg-info">{{ $patient->blood_type }}</span>
                            </td>
                            <td>{{ $patient->date_of_birth ? $patient->date_of_birth->format('d M Y') : '-' }}</td>
                            <td>
                                @if($patient->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" onclick="deletePatient({{ $patient->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">Tidak ada pasien ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $patients->links('pagination::bootstrap-5') }}
    </div>
</div>

<script>
function deletePatient(id) {
    if(confirm('Apakah Anda yakin ingin menghapus pasien ini?')) {
        fetch(`/patients/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        }).then(response => {
            if(response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
