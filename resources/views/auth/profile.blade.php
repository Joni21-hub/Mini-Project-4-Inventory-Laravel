@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page_title', 'Pengaturan Akun & Keamanan')

@section('content')
<div class="row g-4">
    <!-- User summary card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="avatar-initials mx-auto mb-3" style="width: 72px; height: 72px; font-size: 1.8rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold mb-1 text-dark">{{ $user->name }}</h5>
            <p class="text-muted small mb-2">{{ '@' . $user->username }} &bull; {{ $user->email }}</p>
            <div class="mb-4">
                <span class="badge {{ $user->role === 'admin' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} text-uppercase px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                    Hak Akses: {{ $user->role }}
                </span>
            </div>
            
            <hr class="my-3" style="border-color: #f1f5f9;">

            <div class="text-start small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Telepon:</span>
                    <span class="fw-semibold text-dark font-monospace">{{ $user->phone ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Bergabung:</span>
                    <span class="fw-semibold text-dark">{{ $user->created_at->format('d M Y') }}</span>
                </div>
                <div class="mb-1">
                    <span class="text-muted d-block mb-1">Alamat Domisili:</span>
                    <span class="text-dark">{{ $user->address ?? 'Belum ada alamat' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit forms -->
    <div class="col-lg-8">
        <!-- Biodata Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3">
                <h6 class="mb-0 fw-bold text-dark">Perbarui Data Pribadi</h6>
                <small class="text-muted">Kelola identitas akun dan kontak operasional Anda</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" 
                                   value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Alamat Lengkap</label>
                            <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" 
                                      placeholder="Alamat domisili atau operasional">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-2">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h6 class="mb-0 fw-bold text-dark">Ganti Kata Sandi</h6>
                <small class="text-muted">Pastikan akun Anda menggunakan kombinasi sandi yang aman</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Kata Sandi Baru</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-2">
                        <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
