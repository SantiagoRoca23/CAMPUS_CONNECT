<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión · Campus Connect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="brand" style="margin-bottom: 18px; color: var(--ink);">
            <span class="brand-mark">CC</span>
            <div>
                <strong style="color: var(--ink);">Campus Connect</strong>
                <small style="color: var(--ink-soft);">Plataforma de solicitudes</small>
            </div>
        </div>

        <h1>Iniciar sesión</h1>
        <p>Accede para registrar, seguir y gestionar requerimientos institucionales.</p>

        @if($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="stack">
            @csrf
            <label>
                Correo institucional
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </label>
            <label>
                Contraseña
                <input type="password" name="password" required>
            </label>
            <label style="display:flex; align-items:center; gap:8px; font-weight:500;">
                <input type="checkbox" name="remember" style="width:auto;">
                Recordarme
            </label>
            <button class="btn btn-primary" type="submit">Entrar</button>
        </form>

        <div style="margin-top:22px; padding-top:16px; border-top:1px solid var(--line); color: var(--ink-soft); font-size: 0.92rem;">
            <strong>Usuarios demo</strong>
            <p style="margin:8px 0 0;">admin@campus.edu / password</p>
            <p style="margin:4px 0 0;">staff@campus.edu / password</p>
            <p style="margin:4px 0 0;">estudiante@campus.edu / password</p>
        </div>
    </div>
</body>
</html>
