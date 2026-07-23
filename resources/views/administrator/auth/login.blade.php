<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login — SAGAR School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #0d0f1a;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(100, 108, 255, 0.15) 0%, transparent 70%);
            top: -100px;
            left: -100px;
            border-radius: 50%;
            animation: pulse 6s ease-in-out infinite;
        }
        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(167, 139, 250, 0.12) 0%, transparent 70%);
            bottom: -100px;
            right: -100px;
            border-radius: 50%;
            animation: pulse 6s ease-in-out infinite 3s;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 0 16px;
        }

        .login-box {
            background: rgba(19, 22, 36, 0.9);
            border: 1px solid rgba(100, 108, 255, 0.2);
            border-radius: 24px;
            padding: 48px 40px;
            backdrop-filter: blur(20px);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(100, 108, 255, 0.1);
        }

        .login-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #646cff, #a78bfa);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            margin: 0 auto 24px;
            box-shadow: 0 0 32px rgba(100, 108, 255, 0.4);
        }

        .login-title {
            font-size: 26px;
            font-weight: 800;
            color: #e2e8f0;
            text-align: center;
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 13.5px;
            color: #64748b;
            text-align: center;
            margin-bottom: 36px;
        }

        .form-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .form-control {
            background: rgba(30, 34, 54, 0.8);
            border: 1px solid rgba(100, 108, 255, 0.2);
            color: #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }
        .form-control::placeholder { color: #475569; }
        .form-control:focus {
            background: rgba(30, 34, 54, 0.95);
            border-color: #646cff;
            box-shadow: 0 0 0 3px rgba(100, 108, 255, 0.2);
            color: #e2e8f0;
            outline: none;
        }

        .input-group .input-group-text {
            background: rgba(30, 34, 54, 0.8);
            border: 1px solid rgba(100, 108, 255, 0.2);
            color: #64748b;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-radius: 0 10px 10px 0;
            border-left: none;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #646cff, #818cf8);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(100, 108, 255, 0.45);
        }
        .btn-login:hover::before { opacity: 1; }
        .btn-login:active { transform: translateY(0); }

        .form-check-input:checked {
            background-color: #646cff;
            border-color: #646cff;
        }
        .form-check-label {
            color: #94a3b8;
            font-size: 13px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #fca5a5;
            border-radius: 10px;
            font-size: 13.5px;
            padding: 12px 16px;
        }

        .login-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: #334155;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-box">

        <div class="login-logo">A</div>
        <div class="login-title">Selamat Datang</div>
        <div class="login-subtitle">Masuk ke Panel Admin SAGAR</div>

        @if($errors->any())
            <div class="alert-danger mb-4 rounded">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email') }}" required autofocus
                           placeholder="admin@sekolah.sch.id">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password"
                           required placeholder="••••••••">
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check mb-0">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk ke Dashboard
            </button>
        </form>

        <div class="login-footer">
            &copy; {{ date('Y') }} SAGAR School Management System
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
