@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page_title', 'Pengaturan Profil')

@section('content')
<div class="row g-4">
    <!-- User summary card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="mx-auto rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm mb-3" style="width: 80px; height: 80px;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted small mb-2">{{ '@' . $user->username }} &bull; {{ $user->email }}</p>
            <div>
                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} text-uppercase px-3 py-2">
                    <i class="bi {{ $user->role === 'admin' ? 'bi-shield-check' : 'bi-person-check' }} me-1"></i>
                    Hak Akses: {{ $user->role }}
                </span>
            </div>
            <hr class="my-4">
            <div class="text-start small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Telepon:</span>
                    <span class="fw-semibold">{{ $user->phone ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Bergabung:</span>
                    <span class="fw-semibold">{{ $user->created_at->format('d M Y') }}</span>
                </div>
                <div class="mb-1">
                    <span class="text-muted d-block mb-1">Alamat:</span>
                    <span class="text-dark">{{ $user->address ?? 'Belum ada alamat' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit forms -->
    <div class="col-lg-8">
        <!-- Biodata Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Perbarui Data Pribadi</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label small fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label small fw-semibold">Username</label>
                            <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" 
                                   value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-semibold">Alamat Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-semibold">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label small fw-semibold">Alamat Lengkap</label>
                            <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" 
                                      placeholder="Alamat domisili atau operasional">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock-fill text-warning me-2"></i>Ganti Kata Sandi</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="current_password" class="form-label small fw-semibold">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label small fw-semibold">Kata Sandi Baru</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label small fw-semibold">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-warning text-dark px-4 fw-semibold">
                            <i class="bi bi-key me-1"></i> Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
