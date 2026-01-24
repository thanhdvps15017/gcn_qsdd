<nav class="navbar navbar-expand bg-white fixed-top px-3 px-lg-4">
    <div class="d-flex align-items-center gap-3">
        <!-- Toggle (mobile only) -->
        <button class="btn btn-outline-secondary d-lg-none" onclick="toggleSidebar(event)">
            <i class="bi bi-list"></i>
        </button>

        <!-- Logo -->
        <a href="#" class="navbar-brand d-flex align-items-center gap-2 mb-0">
            <div class="logo-box">
                <i class="bi bi-map-fill"></i>
            </div>
            <span class="logo-text">
                Quản lý hồ sơ<br>
                <small>Đất đai</small>
            </span>
        </a>
    </div>

    <!-- User -->
    <div class="ms-auto dropdown">
        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle user-icon"></i>
            <span class="user-name d-none d-md-inline">Nguyễn Văn A</span>
            <i class="bi bi-chevron-down small text-muted"></i>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
                <a class="dropdown-item" href="#">
                    <i class="bi bi-person me-2"></i> Hồ sơ cá nhân
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a class="dropdown-item text-danger" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                </a>

                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>

        </ul>
    </div>
</nav>
