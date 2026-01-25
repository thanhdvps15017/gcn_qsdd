    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quản lý hồ sơ đất đai</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

        <style>
            :root {
                --primary: #1E5AA8;
                --sidebar-width: 260px;
                --navbar-height: 56px;
                --bg-main: #f5f5f5;
            }

            body {
                background: var(--bg-main);
                min-height: 100vh;
            }

            /* ================= NAVBAR ================= */
            .navbar {
                height: var(--navbar-height);
                box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
            }

            .logo-box {
                width: 38px;
                height: 38px;
                background: var(--primary);
                color: #fff;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
            }

            .logo-text {
                font-weight: 700;
                color: var(--primary);
                line-height: 1.1;
            }

            .logo-text small {
                font-size: .75rem;
                font-weight: 500;
                color: #6c757d;
            }

            .user-icon {
                font-size: 1.8rem;
                color: #6c757d;
            }

            .user-name {
                font-weight: 500;
            }

            /* ================= SIDEBAR ================= */
            .sidebar {
                position: fixed;
                top: var(--navbar-height);
                bottom: 0;
                left: -260px;
                width: var(--sidebar-width);
                background: var(--primary);
                color: #fff;
                transition: left .3s ease;
                z-index: 1000;
                overflow-y: auto;
                overflow-x: hidden;
            }

            .sidebar .collapse {
                overflow: visible;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar .nav-link {
                color: #fff;
                padding: .9rem 1.25rem;
            }

            .sidebar .nav-link:hover,
            .sidebar .nav-link.active {
                background: rgba(255, 255, 255, .15);
            }

            /* ================= MAIN ================= */
            .main-content {
                padding-top: calc(var(--navbar-height) + 1rem);
                padding-bottom: 2rem;
            }

            /* ================= OVERLAY ================= */
            .overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .5);
                opacity: 0;
                visibility: hidden;
                transition: .3s;
                z-index: 999;
            }

            .overlay.show {
                opacity: 1;
                visibility: visible;
            }

            /* ================= DESKTOP ================= */
            @media (min-width: 992px) {
                .sidebar {
                    left: 0;
                }

                .main-content {
                    margin-left: var(--sidebar-width);
                }

                .overlay {
                    display: none;
                }
            }


            .table-responsive {
                overflow-x: auto;
            }
        </style>

        @stack('styles')
    </head>

    <body>
        @include('layouts.nav')

        @include('layouts.sidebar')

        <!-- MAIN -->
        <main class="main-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>

        <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
            <!-- Các toast sẽ được append vào đây -->
        </div>

        <!-- JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function toggleSidebar(e) {
                if (e) e.stopPropagation();
                document.getElementById('sidebar').classList.toggle('show');
                document.getElementById('overlay').classList.toggle('show');
            }

            function showToast(message, type = 'success', title = '', duration = 5000) {
                const container = document.getElementById('toastContainer');

                if (!container) {
                    console.error('Không tìm thấy toast container!');
                    return;
                }

                const bg = {
                    success: 'bg-success',
                    error: 'bg-danger',
                    warning: 'bg-warning text-dark',
                    info: 'bg-info'
                } [type] || 'bg-primary';

                const toastId = 'toast-' + Date.now();

                const html = `
                    <div id="${toastId}" class="toast align-items-center text-white ${bg} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                ${title ? `<strong>${title}</strong><br>` : ''}
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', html);

                const toastEl = document.getElementById(toastId);
                if (toastEl) {
                    const toast = new bootstrap.Toast(toastEl, {
                        autohide: true,
                        delay: duration
                    });
                    toast.show();

                    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
                    showToast("{{ addslashes(session('success')) }}", 'success');
                @endif

                @if (session('error'))
                    showToast("{{ addslashes(session('error')) }}", 'error');
                @endif

                @if (session('warning'))
                    showToast("{{ addslashes(session('warning')) }}", 'warning');
                @endif

                @if (session('info'))
                    showToast("{{ addslashes(session('info')) }}", 'info');
                @endif

                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        showToast("{{ addslashes($error) }}", 'error');
                    @endforeach
                @endif
            });
        </script>
        @stack('script')
    </body>

    </html>
