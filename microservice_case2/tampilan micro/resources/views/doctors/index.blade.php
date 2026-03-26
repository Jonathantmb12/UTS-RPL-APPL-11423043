@extends('layouts.app')

@section('title', 'Kelola Dokter')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6"><i class="bi bi-person-badge"></i> Kelola Dokter</h1>
            <p class="text-muted">Kelola data dan jadwal dokter</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('doctors.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Dokter
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('doctors.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="specialization" class="form-select">
                        <option value="">Semua Spesialisasi</option>
                        <option value="Cardiology" @if(request('specialization') === 'Cardiology') selected @endif>Cardiology</option>
                        <option value="Pediatrics" @if(request('specialization') === 'Pediatrics') selected @endif>Pediatrics</option>
                        <option value="Orthopedics" @if(request('specialization') === 'Orthopedics') selected @endif>Orthopedics</option>
                        <option value="Neurology" @if(request('specialization') === 'Neurology') selected @endif>Neurology</option>
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

    <!-- Doctors Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-table"></i> Daftar Dokter</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Spesialisasi</th>
                        <th>No. Lisensi</th>
                        <th>Rumah Sakit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doctor)
                        <tr>
                            <td><strong>{{ $doctor->name }}</strong></td>
                            <td>{{ $doctor->email }}</td>
                            <td><span class="badge bg-primary">{{ $doctor->specialization }}</span></td>
                            <td>{{ $doctor->license_number }}</td>
                            <td>{{ $doctor->hospital_name }}</td>
                            <td>
                                @if($doctor->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('doctors.show', $doctor->id) }}" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" onclick="deleteDoctor({{ $doctor->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">Tidak ada dokter ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $doctors->links('pagination::bootstrap-5') }}
    </div>
</div>

<script>
function deleteDoctor(id) {
    if(confirm('Apakah Anda yakin ingin menghapus dokter ini?')) {
        fetch(`/doctors/${id}`, {
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
