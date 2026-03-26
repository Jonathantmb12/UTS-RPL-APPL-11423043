@extends('layouts.app')

@section('title', isset($patient) ? 'Edit Pasien' : 'Tambah Pasien Baru')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                @if(isset($patient))
                    <i class="bi bi-pencil-square"></i> Edit Pasien
                @else
                    <i class="bi bi-person-plus"></i> Tambah Pasien Baru
                @endif
            </h1>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Pasien</h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($patient) ? route('patients.update', $patient->id) : route('patients.store') }}" method="POST">
                        @csrf
                        @if(isset($patient))
                            @method('PUT')
                        @endif

                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                   value="{{ old('name', $patient->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                                   value="{{ old('email', $patient->email ?? '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password (hanya untuk create) -->
                        @if(!isset($patient))
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" 
                                       name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- Tanggal Lahir & Gender -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" name="date_of_birth" 
                                       value="{{ old('date_of_birth', $patient->date_of_birth ?? '') }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="male" @if(old('gender', $patient->gender ?? '') === 'male') selected @endif>Laki-laki</option>
                                    <option value="female" @if(old('gender', $patient->gender ?? '') === 'female') selected @endif>Perempuan</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Nomor Telepon & Golongan Darah -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Nomor Telepon</label>
                                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                       id="phone_number" name="phone_number" 
                                       value="{{ old('phone_number', $patient->phone_number ?? '') }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Golongan Darah</label>
                                <select class="form-select @error('blood_type') is-invalid @enderror" id="blood_type" name="blood_type" required>
                                    <option value="">Pilih Golongan Darah</option>
                                    <option value="O+" @if(old('blood_type', $patient->blood_type ?? '') === 'O+') selected @endif>O+</option>
                                    <option value="O-" @if(old('blood_type', $patient->blood_type ?? '') === 'O-') selected @endif>O-</option>
                                    <option value="A+" @if(old('blood_type', $patient->blood_type ?? '') === 'A+') selected @endif>A+</option>
                                    <option value="A-" @if(old('blood_type', $patient->blood_type ?? '') === 'A-') selected @endif>A-</option>
                                    <option value="B+" @if(old('blood_type', $patient->blood_type ?? '') === 'B+') selected @endif>B+</option>
                                    <option value="B-" @if(old('blood_type', $patient->blood_type ?? '') === 'B-') selected @endif>B-</option>
                                    <option value="AB+" @if(old('blood_type', $patient->blood_type ?? '') === 'AB+') selected @endif>AB+</option>
                                    <option value="AB-" @if(old('blood_type', $patient->blood_type ?? '') === 'AB-') selected @endif>AB-</option>
                                </select>
                                @error('blood_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" required>{{ old('address', $patient->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alergi & Kontak Darurat -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="allergies" class="form-label">Alergi (Opsional)</label>
                                <input type="text" class="form-control" id="allergies" name="allergies" 
                                       placeholder="Cth: Penisilin, Kacang..." value="{{ old('allergies', $patient->allergies ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact" class="form-label">Kontak Darurat</label>
                                <input type="tel" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                       id="emergency_contact" name="emergency_contact" 
                                       value="{{ old('emergency_contact', $patient->emergency_contact ?? '') }}">
                                @error('emergency_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> @if(isset($patient)) Update @else Simpan @endif
                            </button>
                            <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi</h6>
                </div>
                <div class="card-body text-muted" style="font-size: 0.9rem;">
                    <p><strong>Keamanan Data:</strong> Semua data pasien dienkripsi dan dilindungi sesuai regulasi kesehatan.</p>
                    <p><strong>Password Default:</strong> Pastikan password pasien aman dan mudah diingat.</p>
                    <p><strong>Golongan Darah:</strong> Informasi penting untuk penanganan darurat medis.</p>
                    <hr>
                    <p class="text-danger"><small><i class="bi bi-exclamation-circle"></i> Semua field dengan tanda * wajib diisi.</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
