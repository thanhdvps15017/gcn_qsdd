<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #1E5AA8;
            --primary-rgb: 30, 90, 168;
        }

        body {
            min-height: 100vh;
            background-image: url('{{ $loginBg ? asset("storage/$loginBg") : asset('images/login-default.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* lớp phủ */
        .bg-overlay {
            min-height: 100vh;
            background: rgba(245, 245, 245, 0.88);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #1a4e92;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), .25);
        }

        .card {
            border-radius: 1rem;
            border: none;
        }
    </style>
</head>

<body>
    <div class="bg-overlay">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">

                    <div class="card shadow-lg my-5">
                        <div class="card-body p-4 p-md-5">

                            <h2 class="text-center mb-5 fw-bold text-primary">
                                ĐĂNG NHẬP
                            </h2>

                            <form method="POST" action="{{ url('/login') }}">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label fw-medium">Tài khoản</label>
                                    <input type="text" name="username"
                                        class="form-control form-control-lg @error('username') is-invalid @enderror"
                                        value="{{ old('username') }}" required autofocus>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-medium">Mật khẩu</label>
                                    <input type="password" name="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-primary btn-lg py-3">
                                        Đăng nhập
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>

</html>
