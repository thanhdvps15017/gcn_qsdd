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
        
        <!-- Bootstrap Select -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            :root {
                --primary: #0B5FA5;
                --secondary: #2E7D32;
                --header-bg: #0B5FA5;
                --sidebar-bg: #084C85;
                --sidebar-active: #2E7D32;
                --hover-color: rgba(255, 255, 255, 0.1);
                --bg-main: #F4F8FB;
                --card-bg: #FFFFFF;
                --border-color: #E2E8F0;

                --sidebar-width: 270px;
                --navbar-height: 70px;
                --border-radius-main: 12px;
                --border-radius-sm: 8px;
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: var(--bg-main);
                background-image: 
                    radial-gradient(at 0% 0%, rgba(11, 95, 165, 0.08) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(46, 125, 50, 0.08) 0px, transparent 50%);
                background-attachment: fixed;
                min-height: 100vh;
                color: #1E293B;
            }

            /* ================= NAVBAR ================= */
            .navbar {
                height: var(--navbar-height);
                background: rgba(11, 95, 165, 0.95) !important;
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.15);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            }

            .logo-box {
                width: 38px;
                height: 38px;
                background: rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 10px;
                color: #FFFFFF;
                font-size: 1.2rem;
                box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.1);
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .navbar-brand:hover .logo-box {
                transform: rotate(15deg) scale(1.05);
                background: rgba(255, 255, 255, 0.3);
            }
            .logo-text {
                font-family: 'Plus Jakarta Sans', sans-serif;
                letter-spacing: 0.5px;
            }
            .user-profile-badge {
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.15);
                padding: 6px 14px;
                border-radius: 50px;
                transition: all 0.3s ease;
            }
            .user-profile-badge:hover {
                background: rgba(255, 255, 255, 0.2);
                border-color: rgba(255, 255, 255, 0.3);
                transform: translateY(-1px);
            }

            /* ================= SIDEBAR ================= */
            .sidebar {
                position: fixed;
                top: var(--navbar-height);
                bottom: 0;
                left: -280px;
                width: var(--sidebar-width);
                background: linear-gradient(180deg, rgba(8, 76, 133, 0.95), rgba(4, 42, 74, 0.98)) !important;
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border-right: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 4px 0 24px rgba(0,0,0,0.06);
                transition: left .4s cubic-bezier(0.16, 1, 0.3, 1);
                z-index: 1000;
                overflow-y: auto;
                overflow-x: hidden;
            }

            .sidebar::-webkit-scrollbar { width: 5px; }
            .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
            .sidebar.show { left: 0; }

            /* ================= MAIN ================= */
            .main-content {
                padding-top: calc(var(--navbar-height) + 0.5rem);
                padding-bottom: 0.5rem;
                transition: margin 0.3s ease;
            }

            /* ================= OVERRIDE BOOTSTRAP ================= */
            .card, .modal-content {
                background-color: #FFFFFF;
                border: 1px solid rgba(0, 0, 0, 0.05);
                border-radius: var(--border-radius-main) !important;
                box-shadow: 0 4px 20px rgba(0,0,0,0.03);
                margin-bottom: 0.5rem;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .card:hover {
                box-shadow: 0 8px 30px rgba(11, 95, 165, 0.08);
            }
            .card-header, .modal-header {
                background: linear-gradient(135deg, var(--primary), var(--sidebar-bg)) !important;
                color: #FFFFFF !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
                border-radius: var(--border-radius-main) var(--border-radius-main) 0 0 !important;
                padding: 0.75rem 1rem;
            }
            .card-header > *:not(.btn), .modal-header > *:not(.btn),
            .card-header .text-white, .modal-header .text-white {
                color: #FFFFFF !important;
            }
            .card-header h4, .card-header h5, .modal-header h4, .modal-header h5, .modal-title {
                background: none !important;
                -webkit-text-fill-color: #FFFFFF !important;
                font-weight: 700 !important;
                margin: 0;
                font-size: 1.1rem;
            }
            .modal-header .btn-close {
                filter: invert(1) grayscale(100%) brightness(200%);
            }

            /* Inputs */
            .form-control, .form-select {
                border-color: var(--border-color);
                border-radius: var(--border-radius-sm) !important;
                padding: 0.375rem 0.75rem;
                transition: all 0.3s ease;
            }
            .form-control:focus, .form-select:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 4px rgba(11, 95, 165, 0.12);
            }

            /* Buttons */
            .btn {
                border-radius: var(--border-radius-sm) !important;
                font-weight: 600;
                padding: 0.375rem 0.75rem;
                transition: all 0.3s ease;
            }
            .btn-primary { 
                background: linear-gradient(135deg, var(--primary), #1976D2) !important; 
                border: none !important; 
                color: white !important; 
                box-shadow: 0 4px 10px rgba(11, 95, 165, 0.25);
            }
            .btn-primary:hover { 
                transform: translateY(-1px);
                box-shadow: 0 6px 15px rgba(11, 95, 165, 0.35);
            }
            
            .btn-success { 
                background: linear-gradient(135deg, var(--secondary), #4CAF50) !important; 
                border: none !important; 
                color: white !important; 
                box-shadow: 0 4px 10px rgba(46, 125, 50, 0.25);
            }
            .btn-success:hover { 
                transform: translateY(-1px);
                box-shadow: 0 6px 15px rgba(46, 125, 50, 0.35);
            }

            /* Tables */
            .table-responsive { border-radius: var(--border-radius-main); overflow: hidden; }
            .table { margin-bottom: 0; border-collapse: separate; border-spacing: 0; }
            .table thead th {
                background-color: #F8FAFC;
                color: #64748B;
                font-weight: 700;
                text-transform: uppercase;
                font-size: 0.8rem;
                letter-spacing: 0.5px;
                border-bottom: 2px solid var(--border-color);
                padding: 0.5rem 0.75rem;
            }
            .table tbody td {
                padding: 0.5rem 0.75rem;
                vertical-align: middle;
                border-bottom: 1px solid var(--border-color);
                color: #334155;
            }

            /* ================= OVERLAY ================= */
            .overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.5);
                opacity: 0;
                visibility: hidden;
                transition: .4s;
                z-index: 999;
            }
            .overlay.show { opacity: 1; visibility: visible; }

            /* ================= DESKTOP ================= */
            @media (min-width: 992px) {
                .sidebar { left: 0; }
                .main-content { margin-left: var(--sidebar-width); }
                .overlay { display: none; }
            }
        </style>
        
        <style>
            /* Custom Bootstrap Select to match border-radius */
            .bootstrap-select .dropdown-toggle {
                border-radius: var(--border-radius-sm) !important;
                border-color: var(--border-color) !important;
                background-color: #FFFFFF !important;
            }
            .bootstrap-select .dropdown-toggle:focus {
                border-color: var(--primary) !important;
                box-shadow: 0 0 0 4px rgba(11, 95, 165, 0.12) !important;
                outline: none !important;
            }
            .bootstrap-select .dropdown-menu {
                border-radius: var(--border-radius-sm) !important;
                border: 1px solid var(--border-color) !important;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important;
                z-index: 1060 !important;
            }
            /* Fix selectpicker background color when disabled */
            .bootstrap-select.disabled .dropdown-toggle {
                background-color: #e9ecef !important;
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
                @yield('content')
            </div>
        </main>

        <!-- JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
        
        <script>
            function toggleSidebar(e) {
                if (e) e.stopPropagation();
                document.getElementById('sidebar').classList.toggle('show');
                document.getElementById('overlay').classList.toggle('show');
            }

            function showToast(message, type = 'success', title = '', duration = 5000) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: duration,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                // Chuyển đổi type sao cho khớp với SweetAlert2
                let iconType = 'success';
                if (type === 'error' || type === 'danger') iconType = 'error';
                else if (type === 'warning') iconType = 'warning';
                else if (type === 'info') iconType = 'info';

                Toast.fire({
                    icon: iconType,
                    title: title ? `<strong>${title}</strong><br>${message}` : message
                });
            }

            function confirmDelete(event, form, message) {
                event.preventDefault();
                Swal.fire({
                    title: 'Xác nhận xoá?',
                    text: message || "Hành động này không thể hoàn tác!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Có, xoá!',
                    cancelButtonText: 'Huỷ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            // Đồng bộ hoá tự động khởi tạo khi trang tải xong
            $(document).ready(function() {
                // Tự động biến tất cả thẻ select thành selectpicker có tìm kiếm, ép width 100% để không lỗi UI
                $('select:not(.no-selectpicker)')
                    .removeClass('form-select form-control')
                    .attr('data-live-search', 'true')
                    .attr('data-width', '100%')
                    .selectpicker({
                        styleBase: 'form-control',
                        style: ''
                    });

                // Xử lý flash messages
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

            });
        </script>
        @stack('script')
    </body>

    </html>
