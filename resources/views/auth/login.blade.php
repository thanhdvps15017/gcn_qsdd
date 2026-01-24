<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        :root {
            --primary: #1E5AA8;
            --primary-rgb: 30, 90, 168;
            --sidebar-width: 260px;
            --navbar-height: 56px;
            --bg-main: #f5f5f5;
        }

        body {
            background-color: var(--bg-main);
            min-height: 100vh;
        }

        /* Override Bootstrap primary */
        .btn-primary,
        .bg-primary,
        .text-primary,
        .border-primary {
            --bs-primary: var(--primary);
            --bs-primary-rgb: var(--primary-rgb);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #1a4e92;
            border-color: #1a4e92;
        }

        .btn-primary:focus,
        .btn-primary:active {
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.25);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.25);
        }

        .invalid-feedback {
            color: #dc3545;
        }

        .card {
            border: none;
            border-radius: 1rem;
        }
    </style>
</head>

<body>

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

                            <!-- Username -->
                            <div class="mb-4">
                                <label for="username" class="form-label fw-medium">Tài khoản</label>
                                <input type="text"
                                    class="form-control form-control-lg @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username') }}"
                                    placeholder="Nhập tên tài khoản" required autofocus>
                                @error('username')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium">Mật khẩu</label>
                                <input type="password"
                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Nhập mật khẩu" required>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Remember Me + Forgot Password -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-medium py-3">
                                    Đăng nhập
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
