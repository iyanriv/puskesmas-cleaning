<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIKAP Bersih | Sistem Informasi Kebersihan dan Penilaian</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sikapbersih.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-sikapbersih.png') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', sans-serif;
        }
        .touch-friendly {
            min-height: 90px;
            border-radius: 16px;
            transition: all 0.2s ease-in-out;
        }
        .touch-friendly:active {
            transform: scale(0.98);
        }
        .icon-large {
            font-size: 2.5rem;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}" style="color: #31999E;">
                    <img src="{{ asset('images/logo-sikapbersih.png') }}" alt="Logo SIKAP Bersih" style="height: 34px; margin-right: 8px; object-fit: contain;"> SIKAP Bersih
                </a>
                
                <div class="d-flex align-items-center">
                    @auth
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="container pb-5">
            @yield('content')
        </main>
    </div>
</body>
</html>