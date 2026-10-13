<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Masuk — InvenTech Enterprise</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading: 'Outfit', 'Plus Jakarta Sans', sans-serif;
            --grad-primary: linear-gradient(135deg, #4338ca 0%, #6366f1 55%, #4f46e5 100%);
            --glow-primary: 0 8px 25px rgba(99, 102, 241, 0.4);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background: #090d16;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(6, 182, 212, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(15, 23, 42, 0.8) 0%, transparent 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #f1f5f9;
            letter-spacing: -0.01em;
        }

        .login-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.1);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-header {
            background: linear-gradient(135deg, #090d16 0%, #1e1b4b 100%);
            padding: 2.25rem 2rem 2rem;
            color: #ffffff;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
        }

        .brand-header .brand-title {
            font-family: var(--font-heading);
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 40%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin-bottom: 0.35rem;
        }

        .brand-header .brand-subtitle {
            font-size: 0.78rem;
            color: #94a3b8;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-weight: 600;
            margin: 0;
        }

        .login-body {
            padding: 2rem 2rem 2.25rem;
            color: #1e293b;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 0.65rem;
            padding: 0.65rem 0.95rem;
            font-size: 0.9rem;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }

        .btn-primary-custom {
            background: var(--grad-primary);
            color: #ffffff;
            font-weight: 700;
            padding: 0.75rem;
            border-radius: 0.65rem;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: var(--glow-primary);
            transition: all 0.22s ease;
            font-size: 0.92rem;
            letter-spacing: -0.01em;
            width: 100%;
        }

        .btn-primary-custom:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
        }

        .btn-primary-custom:active {
            transform: scale(0.98);
        }

        .demo-chip {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1.5px dashed #cbd5e1;
            background-color: #f8fafc;
            padding: 0.65rem 0.75rem;
            border-radius: 0.65rem;
            text-align: center;
        }

        .demo-chip:hover {
            background-color: #eef2ff;
            border-color: #818cf8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.12);
        }

        .demo-chip:active {
            transform: scale(0.97);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-header">
        <h2 class="brand-title">INVENTECH</h2>
        <p class="brand-subtitle">Enterprise Cloud Inventory</p>
    </div>

    <div class="login-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small mb-3 p-2 rounded-2" role="alert" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;">
                {{ session('success') }}
                <button type="button" class="btn-close btn-sm p-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show small mb-3 p-2 rounded-2" role="alert" style="background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239;">
                {{ session('error') }}
                <button type="button" class="btn-close btn-sm p-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->has('login'))
            <div class="alert alert-danger alert-dismissible fade show small mb-3 p-2 rounded-2" role="alert" style="background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239;">
                {{ $errors->first('login') }}
                <button type="button" class="btn-close btn-sm p-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="login" class="form-label">Username atau Email</label>
                <input type="text" name="login" id="login" class="form-control @error('login') is-invalid @enderror" 
                       placeholder="admin atau admin@inventory.test" value="{{ old('login') }}" required autofocus>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0">Kata Sandi (Password)</label>
                    <button class="btn btn-link p-0 text-decoration-none text-muted" type="button" id="togglePassword" style="font-size: 0.75rem;">
                        <span id="toggleText">Lihat Sandi</span>
                    </button>
                </div>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                       placeholder="••••••••" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small text-muted" for="remember">
                        Ingat Saya di Perangkat Ini
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-custom mb-3">
                Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-4 pt-3 border-top">
            <span class="d-block text-muted text-center small mb-2 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">Akun Demo (Klik untuk Isi Cepat):</span>
            <div class="d-flex gap-2">
                <div class="demo-chip flex-fill" onclick="fillCredentials('admin', 'admin123')">
                    <div class="fw-bold text-danger mb-0" style="font-size: 0.85rem;">Admin</div>
                    <span class="text-muted font-monospace" style="font-size: 0.72rem;">admin123</span>
                </div>
                <div class="demo-chip flex-fill" onclick="fillCredentials('staff', 'staff123')">
                    <div class="fw-bold text-primary mb-0" style="font-size: 0.85rem;">Staff Gudang</div>
                    <span class="text-muted font-monospace" style="font-size: 0.72rem;">staff123</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const toggleBtn = document.getElementById('togglePassword');
    const passInput = document.getElementById('password');
    const toggleText = document.getElementById('toggleText');

    toggleBtn.addEventListener('click', () => {
        if (passInput.type === 'password') {
            passInput.type = 'text';
            toggleText.textContent = 'Sembunyikan';
        } else {
            passInput.type = 'password';
            toggleText.textContent = 'Lihat Sandi';
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
