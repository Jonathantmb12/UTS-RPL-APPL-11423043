@extends('layouts.app')

@section('title', 'Detail Pasien - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6"><i class="bi bi-person"></i> {{ $patient->name }}</h1>
            <p class="text-muted">ID Pasien: #{{ $patient->id }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                <i class="bi bi-person-vcard"></i> Data Pribadi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="health-tab" data-bs-toggle="tab" data-bs-target="#health" type="button" role="tab">
                <i class="bi bi-heart-pulse"></i> Riwayat Kesehatan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab">
                <i class="bi bi-calendar-check"></i> Appointment
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab">
                <i class="bi bi-capsule"></i> Resep
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Tab 1: Data Pribadi -->
        <div class="tab-pane fade show active" id="personal" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Pribadi</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Nama Lengkap</th>
                                    <td>{{ $patient->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><a href="mailto:{{ $patient->email }}">{{ $patient->email }}</a></td>
                                </tr>
                                <tr>
                                    <th>Nomor Telepon</th>
                                    <td><a href="tel:{{ $patient->phone_number }}">{{ $patient->phone_number }}</a></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td>{{ $patient->date_of_birth?->format('d M Y') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>{{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                                <tr>
                                    <th>Usia</th>
                                    <td>{{ $patient->date_of_birth ? now()->diff($patient->date_of_birth)->y : '-' }} tahun</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Medis</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Golongan Darah</th>
                                    <td><span class="badge bg-danger" style="font-size: 1rem;">{{ $patient->blood_type }}</span></td>
                                </tr>
                                <tr>
                                    <th>Alergi</th>
                                    <td>{{ $patient->allergies ?? 'Tidak ada' }}</td>
                                </tr>
                                <tr>
                                    <th>Kontak Darurat</th>
                                    <td>{{ $patient->emergency_contact ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($patient->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Verifikasi</th>
                                    <td>
                                        @if($patient->is_verified)
                                            <span class="badge bg-info">Terverifikasi</span>
                                        @else
                                            <span class="badge bg-warning">Menunggu Verifikasi</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Alamat</h5>
                        </div>
                        <div class="card-body">
                            {{ $patient->address ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Riwayat Kesehatan -->
        <div class="tab-pane fade" id="health" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Catatan Kesehatan</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editHealthModal">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                </div>
                <div class="card-body">
                    @if($healthRecord)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Riwayat Penyakit:</strong>
                                <p>{{ $healthRecord->medical_history ?? 'Tidak ada' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Obat-obatan Saat Ini:</strong>
                                <p>{{ $healthRecord->current_medications ?? 'Tidak ada' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Riwayat Operasi:</strong>
                                <p>{{ $healthRecord->previous_surgeries ?? 'Tidak ada' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Riwayat Keluarga:</strong>
                                <p>{{ $healthRecord->family_history ?? 'Tidak ada' }}</p>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Tanda Vital Terakhir:</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-number">{{ $healthRecord->height_cm ?? '-' }}</div>
                                    <div class="stat-label">Tinggi (cm)</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-number">{{ $healthRecord->weight_kg ?? '-' }}</div>
                                    <div class="stat-label">Berat (kg)</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-number">{{ $healthRecord->blood_pressure_systolic ?? '-' }}/{{ $healthRecord->blood_pressure_diastolic ?? '-' }}</div>
                                    <div class="stat-label">Tekanan Darah</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-number">{{ $healthRecord->heart_rate ?? '-' }}</div>
                                    <div class="stat-label">Detak Jantung (bpm)</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Tidak ada catatan kesehatan</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab 3: Appointment -->
        <div class="tab-pane fade" id="appointments" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Appointment ({{ $appointments->count() }})</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Dokter</th>
                                <th>Spesialisasi</th>
                                <th>Tipe Konsultasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appt)
                                <tr>
                                    <td>{{ $appt->appointment_date->format('d M Y H:i') }}</td>
                                    <td>{{ $appt->doctor->name }}</td>
                                    <td><span class="badge bg-primary">{{ $appt->doctor->specialization }}</span></td>
                                    <td>{{ $appt->consultation_type }}</td>
                                    <td>
                                        <span class="badge-status badge-{{ $appt->status }}">{{ ucfirst($appt->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada appointment</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 4: Resep -->
        <div class="tab-pane fade" id="prescriptions" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Resep ({{ $prescriptions->count() }})</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Obat</th>
                                <th>Dosage</th>
                                <th>Frekuensi</th>
                                <th>Dokter</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prescriptions as $rx)
                                <tr>
                                    <td><strong>{{ $rx->medication_name }}</strong></td>
                                    <td>{{ $rx->dosage }}</td>
                                    <td>{{ $rx->frequency }}</td>
                                    <td>{{ $rx->doctor->name }}</td>
                                    <td>{{ $rx->prescribed_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge-status badge-{{ $rx->status }}">{{ ucfirst($rx->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada resep</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Health Record -->
<div class="modal fade" id="editHealthModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Catatan Kesehatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tinggi (cm)</label>
                        <input type="number" class="form-control" name="height_cm" step="0.1" value="{{ $healthRecord->height_cm ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Berat (kg)</label>
                        <input type="number" class="form-control" name="weight_kg" step="0.1" value="{{ $healthRecord->weight_kg ?? '' }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tekanan Darah Sistol</label>
                            <input type="number" class="form-control" name="blood_pressure_systolic" value="{{ $healthRecord->blood_pressure_systolic ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tekanan Darah Diastol</label>
                            <input type="number" class="form-control" name="blood_pressure_diastolic" value="{{ $healthRecord->blood_pressure_diastolic ?? '' }}">
                        </div>
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
@endsection
