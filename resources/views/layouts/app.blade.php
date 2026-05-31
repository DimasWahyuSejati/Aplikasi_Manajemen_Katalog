<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Web')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/katalog.css?v=' . time()) }}">
</head>
<body>

    <div id="wrapper">
        <div id="sidebar">
            <div class="sidebar-header text-nowrap">
                <i class="fa-solid fa-store me-2"></i> Toko Sepatu
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high me-3"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ url('/katalog') }}" class="{{ request()->is('katalog') ? 'active' : '' }}">
                        <i class="fa-solid fa-shoe-prints me-3"></i> Katalog Produk
                    </a>
                </li>
                <li>
                    <a href="{{ url('/kategori') }}" class="{{ request()->is('kategori') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags me-3"></i> Kategori Sepatu
                    </a>
                </li>
                <li>
                    <a href="{{ url('/merek') }}" class="{{ request()->is('merek') ? 'active' : '' }}">
                        <i class="fa-solid fa-copyright me-3"></i> Manajemen Merek
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-chart-simple me-3"></i> Laporan Penjualan
                    </a>
                </li>
            </ul>
        </div>

        <div id="content-area">
            <div class="top-navbar">
                <h5 class="m-0 fw-bold text-dark">Aplikasi > <span class="text-muted">@yield('header_title', 'Dashboard')</span></h5>
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=1e3a8a&color=fff" alt="User" class="rounded-circle me-2" width="35">
                    <span class="fw-bold me-4 text-dark">Halo, Admin!</span>
                    <a href="#" onclick="logout(); return false;" class="text-danger text-decoration-none fw-bold">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                    </a>
                </div>
            </div>

            <div class="dashboard-content">
                @yield('content')
            </div>
        </div>
    </div> 

    @yield('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/script.js?v=' . time()) }}"></script>
    
    <script>
        // Auth Check
        const token = localStorage.getItem('token');
        const currentPath = window.location.pathname;
        
        if (!token && currentPath !== '/' && currentPath !== '/login') {
            window.location.href = "{{ url('/') }}"; // Redirect to login
        }

        // Set username in navbar if exists
        const username = localStorage.getItem('username');
        if (username) {
            const userElements = document.querySelectorAll('.fw-bold.me-4.text-dark');
            userElements.forEach(el => el.innerText = 'Halo, ' + username + '!');
        }

        // Logout logic
        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('username');
            window.location.href = "{{ url('/') }}";
        }

        // Helper function for API calls with token
        window.fetchWithAuth = function(url, options = {}) {
            const headers = new Headers(options.headers || {});
            headers.append('Authorization', 'Bearer ' + token);
            headers.append('Content-Type', 'application/json');
            
            return fetch(url, {
                ...options,
                headers: headers
            }).then(response => {
                if (response.status === 401) {
                    logout(); // Token expired or invalid
                    throw new Error('Sesi berakhir, silakan login kembali.');
                }
                return response;
            });
        };
    </script>
    
    @yield('scripts')

</body>
</html>