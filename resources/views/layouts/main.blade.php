<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Si-Pita</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #eef2ff;
            min-height: 100vh;
        }
        .navbar-custom {
            background-color: #ffffff;
            border-bottom: 2px solid #000000;
        }
    </style>
</head>
<body>

    <!-- Top Navbar dengan Tombol Hamburger -->
    <nav class="navbar navbar-custom px-3 py-2 sticky-top">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <!-- Tombol Hamburger -->
                <button class="btn btn-light border-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <a href="{{ url('/') }}" class="d-inline-flex align-items-center me-2 text-decoration-none">
                    <img src="{{ asset('images/Diskominfo.webp') }}" alt="Logo Diskominfo" style="height: 36px; width: auto;">
                </a>
                <span class="fs-4 fw-bold text-primary mb-0">Si-Pita Admin</span>
            </div>
            
            <div>
                <a href="{{ route('landing') }}" class="btn btn-outline-dark btn-sm rounded-pill fw-semibold">
                    <i class="bi bi-globe me-1"></i> Ke Halaman Publik
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar Offcanvas (Bisa Buka - Tutup / Hamburger Menu) -->
    @include('components.sidebar')

    <!-- Content Area -->
    <main class="p-4">
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
