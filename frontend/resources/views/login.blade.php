<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pegawai - Toko Sepatu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/api-config.js?v=' . time()) }}"></script>
    <script src="{{ asset('js/helpers.js?v=' . time()) }}"></script>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <i class="fa-solid fa-shoe-prints fa-3x text-primary mb-3"></i>
            <h3 class="fw-bold">Login Sistem</h3>
            <p class="text-muted">Manajemen Katalog Toko Sepatu</p>
        </div>
        
        <form id="form-login">
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                    <input type="text" id="username" class="form-control" placeholder="admin" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" class="form-control" placeholder="******" required>
                </div>
            </div>
            <button type="submit" id="btn-login" class="btn btn-primary w-100 fw-bold py-2 mb-3">MASUK</button>
            <div class="text-center">
                <span class="text-muted">Belum punya akun? </span>
                <a href="{{ url('/register') }}" class="text-decoration-none fw-bold">Daftar sekarang</a>
            </div>
            <div id="login-error" class="text-danger text-center mt-3 d-none fw-bold"></div>
        </form>
    </div>

    <script>
        document.getElementById('form-login').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnLogin = document.getElementById('btn-login');
            const errorDiv = document.getElementById('login-error');
            
            setButtonLoading(btnLogin, true);
            errorDiv.classList.add('d-none');

            const payload = {
                username: document.getElementById('username').value,
                password: document.getElementById('password').value
            };

            fetch(API_ENDPOINTS.login, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if(!response.ok) throw new Error('Username atau password salah');
                return response.json();
            })
            .then(data => {
                if(data.token) {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('username', data.username);
                    window.location.href = "{{ url('/dashboard') }}";
                }
            })
            .catch(error => {
                errorDiv.innerText = error.message;
                errorDiv.classList.remove('d-none');
                setButtonLoading(btnLogin, false, 'MASUK');
            });
        });
    </script>
</body>
</html>