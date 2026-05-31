<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pegawai - Toko Sepatu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <i class="fa-solid fa-shoe-prints fa-3x text-primary mb-3"></i>
            <h3 class="fw-bold">Login Sistem</h3>
            <p class="text-muted">Manajemen Katalog Toko Sepatu</p>
        </div>
        
        <form action="{{ url('/dashboard') }}" method="GET">
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                    <input type="text" class="form-control" placeholder="admin" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" class="form-control" placeholder="******" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">MASUK</button>
        </form>
    </div>

</body>
</html>