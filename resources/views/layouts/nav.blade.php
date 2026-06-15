<nav class="navbar navbar-expand fixed-top px-3 px-lg-4">
    <div class="d-flex align-items-center gap-3">
        <!-- Toggle (mobile only) -->
        <button class="btn btn-outline-light d-lg-none border-0 bg-transparent" onclick="toggleSidebar(event)">
            <i class="bi bi-list fs-3"></i>
        </button>

        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center gap-2.5 mb-0 text-decoration-none gap-2">
            <div class="logo-box flex-shrink-0 d-flex align-items-center justify-content-center">
                <i class="bi bi-map-fill"></i>
            </div>
            <span class="logo-text fw-bold text-white lh-sm fs-6">
                QUẢN LÝ HỒ SƠ<br>
                <small class="fw-normal text-white-50 fs-7">Đất đai & Bản đồ</small>
            </span>
        </a>
    </div>

    <!-- User -->
    <div class="ms-auto dropdown">
        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none user-profile-badge" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle fs-5 text-white"></i>
            <span class="d-none d-md-inline fw-semibold text-white fs-7">{{ Auth::user()->name }}</span>
            <i class="bi bi-chevron-down text-white-50 fs-8"></i>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2 mt-2" style="border-radius: 12px;">
            <li>
                <div class="px-3 py-1.5 border-bottom mb-1">
                    <span class="d-block text-dark fw-bold small">{{ Auth::user()->name }}</span>
                    <span class="d-block text-muted fs-7">{{ Auth::user()->email }}</span>
                </div>
            </li>
            <li>
                <a class="dropdown-item text-danger d-flex align-items-center gap-2 py-2 fw-medium" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </a>

                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</nav>
