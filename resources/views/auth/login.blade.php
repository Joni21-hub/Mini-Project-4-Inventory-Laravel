<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Management System</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.25);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
        }

        .brand-header {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            padding: 2.25rem 2rem;
            color: #ffffff;
            text-align: center;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
            backdrop-filter: blur(4px);
        }

        .form-floating > .form-control:focus ~ label {
            color: #4f46e5;
        }

        .btn-primary-custom {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            transform: translateY(-1px);
        }

        .demo-chip {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px dashed #cbd5e1;
            background-color: #f8fafc;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.785rem;
        }

        .demo-chip:hover {
            background-color: #e0e7ff;
            border-color: #6366f1;
            color: #3730a3;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-header">
        <div class="brand-icon">
            <i class="bi bi-boxes"></i>
        </div>
        <h4 class="fw-bold mb-1">InvenTech Laravel</h4>
        <p class="mb-0 text-white-50 small">Sistem Manajemen Inventaris & Mutasi Stok</p>
    </div>

    <div class="p-4 p-md-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show small mb-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->has('login'))
            <div class="alert alert-danger alert-dismissible fade show small mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $errors->first('login') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="login" class="form-label small fw-semibold text-secondary">Username atau Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" name="login" id="login" class="form-control @error('login') is-invalid @enderror" 
                           placeholder="admin atau admin@inventory.test" value="{{ old('login') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="password" class="form-label small fw-semibold text-secondary mb-1">Kata Sandi (Password)</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="••••••••" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small text-muted" for="remember">
                        Ingat Saya di Perangkat Ini
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 mb-3 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-3 pt-3 border-top">
            <span class="d-block text-muted text-center small mb-2 fw-semibold">Akun Uji Coba Cepat (Klik untuk Isi):</span>
            <div class="d-flex gap-2">
                <div class="demo-chip flex-fill text-center" onclick="fillCredentials('admin', 'admin123')">
                    <div class="fw-bold text-dark"><i class="bi bi-shield-lock-fill text-danger me-1"></i>Admin</div>
                    <span class="text-muted" style="font-size: 0.72rem;">admin / admin123</span>
                </div>
                <div class="demo-chip flex-fill text-center" onclick="fillCredentials('staff', 'staff123')">
                    <div class="fw-bold text-dark"><i class="bi bi-person-badge-fill text-primary me-1"></i>Staff</div>
                    <span class="text-muted" style="font-size: 0.72rem;">staff / staff123</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const toggleBtn = document.getElementById('togglePassword');
    const passInput = document.getElementById('password');
    const passIcon = document.getElementById('toggleIcon');

    toggleBtn.addEventListener('click', () => {
        if (passInput.type === 'password') {
            passInput.type = 'text';
            passIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passInput.type = 'password';
            passIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    function fillCredentials(username, pass) {
        document.getElementById('login').value = username;
        document.getElementById('password').value = pass;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
