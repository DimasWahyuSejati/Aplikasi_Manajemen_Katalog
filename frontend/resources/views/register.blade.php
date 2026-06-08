<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Pegawai - Toko Sepatu</title>
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
            <h3 class="fw-bold">Registrasi</h3>
            <p class="text-muted">Buat akun admin baru</p>
        </div>
        
        <form id="form-register">
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                    <input type="text" id="username" class="form-control" autocomplete="off" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" class="form-control" autocomplete="new-password" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Konfirmasi Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password_confirm" class="form-control" autocomplete="new-password" required>
                </div>
            </div>
            <button type="submit" id="btn-register" class="btn btn-primary w-100 fw-bold py-2 mb-3">DAFTAR</button>
            <div class="text-center">
                <span class="text-muted">Sudah punya akun? </span>
                <a href="{{ url('/') }}" class="text-decoration-none fw-bold">Login di sini</a>
            </div>
            <div id="register-error" class="text-danger text-center mt-3 invisible fw-bold" style="min-height: 24px;"></div>
        </form>
    </div>

    <script>
        document.getElementById('form-register').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnRegister = document.getElementById('btn-register');
            const errorDiv = document.getElementById('register-error');
            
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;

            if (password !== passwordConfirm) {
                errorDiv.innerText = 'Konfirmasi password tidak cocok';
                errorDiv.classList.remove('invisible');
                return;
            }

            setButtonLoading(btnRegister, true);
            errorDiv.classList.add('invisible');

            const payload = {
                username: document.getElementById('username').value,
                password: password
            };

            fetch(API_ENDPOINTS.register, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if(!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Gagal registrasi') });
                }
                return response.json();
            })
            .then(data => {
                // Setelah register berhasil, panggil sweet alert atau langsung arahkan ke login
                showSuccess('Registrasi Berhasil', 'Akun berhasil dibuat! Silakan login.', () => {
                    window.location.href = "{{ url('/') }}";
                });
            })
            .catch(error => {
                errorDiv.innerText = error.message;
                errorDiv.classList.remove('invisible');
                setButtonLoading(btnRegister, false, 'DAFTAR');
            });
        });
    </script>
</body>
</html>
