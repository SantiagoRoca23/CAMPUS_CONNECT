<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Campus Connect')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <span class="brand-mark">CC</span>
                <div>
                    <strong>Campus Connect</strong>
                    <small>Gestión universitaria</small>
                </div>
            </div>

            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('solicitudes.index') }}" class="{{ request()->routeIs('solicitudes.*') ? 'active' : '' }}">Solicitudes</a>
                @if(auth()->user()->isStaff())
                    <a href="{{ route('recursos.index') }}" class="{{ request()->routeIs('recursos.*') ? 'active' : '' }}">Recursos</a>
                    <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">Reportes</a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="user-chip">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->role->label() }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost">Cerrar sesión</button>
                </form>
            </div>
        </aside>

        <main class="content">
            <header class="topbar">
                <div>
                    <h1>@yield('heading')</h1>
                    <p>@yield('subtitle')</p>
                </div>
                <div class="topbar-actions">
                    @yield('actions')
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
