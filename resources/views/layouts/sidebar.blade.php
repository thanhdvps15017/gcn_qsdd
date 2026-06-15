<aside class="sidebar" id="sidebar" onclick="event.stopPropagation()">
    <ul class="nav flex-column pt-3 sidebar-menu">

        {{-- ================= Hồ sơ ================= --}}
        @can('ho-so.create')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ho-so.create') ? 'active' : '' }}"
                    href="{{ route('ho-so.create') }}">
                    <i class="bi bi-plus-circle me-2"></i> Thêm hồ sơ
                </a>
            </li>
        @endcan

        @can('ho-so.index')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ho-so.index') ? 'active' : '' }}"
                    href="{{ route('ho-so.index') }}">
                    <i class="bi bi-list-ul me-2"></i> Quản lý hồ sơ
                </a>
            </li>
        @endcan

        {{-- ================= Sổ theo dõi ================= --}}
        @can('so-theo-doi.index')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('so-theo-doi.*') ? 'active' : '' }}"
                    href="{{ route('so-theo-doi.index') }}">
                    <i class="bi bi-journal-bookmark me-2"></i> Sổ theo dõi
                </a>
            </li>
        @endcan

        {{-- ================= Xuất file ================= --}}
        @php
            $exportOpen = request()->routeIs('xuat-excel.*') || request()->routeIs('xuat-word.*');
        @endphp

        @canany(['xuat-excel.index', 'xuat-word.index'])
            <li class="nav-item has-submenu {{ $exportOpen ? 'open' : '' }}">
                <a class="nav-link submenu-toggle" href="javascript:void(0)">
                    <span>
                        <i class="bi bi-file-earmark-arrow-down me-2"></i> Xuất file
                    </span>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>

                <ul class="submenu">
                    @can('xuat-excel.index')
                        <li>
                            <a class="nav-link {{ request()->routeIs('xuat-excel.*') ? 'active' : '' }}"
                                href="{{ route('xuat-excel.index') }}">
                                Xuất Excel
                            </a>
                        </li>
                    @endcan

                    @can('xuat-word.index')
                        <li>
                            <a class="nav-link {{ request()->routeIs('xuat-word.*') ? 'active' : '' }}"
                                href="{{ route('xuat-word.index') }}">
                                Xuất Word
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        {{-- ================= Cài đặt ================= --}}
        @php
            $settingOpen = request()->routeIs([
                'settings.roles.*',
                'settings.users.*',
                'settings.loai-ho-so.*',
                'settings.loai-thu-tuc.*',
                'settings.xa.*',
                'settings.mau-word.*',
                'settings.login-bg.*',
            ]);
        @endphp

        <li class="nav-item has-submenu {{ $settingOpen ? 'open' : '' }}">
            <a class="nav-link submenu-toggle" href="javascript:void(0)">
                <span>
                    <i class="bi bi-gear me-2"></i> Cài đặt
                </span>
                <i class="bi bi-chevron-down arrow"></i>
            </a>

            <ul class="submenu">
                @can('settings.roles.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('settings.roles.*') ? 'active' : '' }}"
                            href="{{ route('settings.roles.index') }}">
                            Phân quyền
                        </a>
                    </li>
                @endcan

                @can('settings.users.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('settings.users.*') ? 'active' : '' }}"
                            href="{{ route('settings.users.index') }}">
                            Tài khoản
                        </a>
                    </li>
                @endcan

                @can('settings.loai-ho-so.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('settings.loai-ho-so.*') ? 'active' : '' }}"
                            href="{{ route('settings.loai-ho-so.index') }}">
                            Loại hồ sơ
                        </a>
                    </li>
                @endcan

                @can('settings.loai-thu-tuc.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('settings.loai-thu-tuc.*') ? 'active' : '' }}"
                            href="{{ route('settings.loai-thu-tuc.index') }}">
                            Loại thủ tục
                        </a>
                    </li>
                @endcan

                @can('settings.xa.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('settings.xa.*') ? 'active' : '' }}"
                            href="{{ route('settings.xa.index') }}">
                            Xã - phường
                        </a>
                    </li>
                @endcan

                @can('settings.mau-word.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('settings.mau-word.*') ? 'active' : '' }}"
                            href="{{ route('settings.mau-word.index') }}">
                            Template Word
                        </a>
                    </li>
                @endcan

                @can('settings.login-bg.edit')
                    <li>
                        <a class="nav-link {{ request()->routeIs('settings.login-bg.*') ? 'active' : '' }}"
                            href="{{ route('settings.login-bg.edit') }}">
                            Cài đặt chung
                        </a>
                    </li>
                @endcan
            </ul>
        </li>

    </ul>
</aside>

{{-- ================= Style ================= --}}
<style>
    .sidebar-menu {
        padding: 10px 0;
    }

    .sidebar ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.75);
        padding: 0.7rem 1.1rem;
        font-weight: 500;
        border-radius: 10px;
        margin: 0.2rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.875rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar .nav-link i:first-child {
        font-size: 1.15rem;
        transition: transform 0.25s ease;
    }

    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.08);
        color: #FFFFFF;
        transform: translateX(4px);
    }

    .sidebar .nav-link:hover i:first-child {
        transform: scale(1.1);
    }

    .sidebar .nav-link.active {
        background: linear-gradient(135deg, var(--secondary), #4CAF50) !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(46, 125, 50, 0.4);
        font-weight: 600;
    }

    .has-submenu .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding-left: 1.5rem;
        margin-left: 1.5rem;
        border-left: 1px dashed rgba(255, 255, 255, 0.15);
    }

    .has-submenu.open .submenu {
        max-height: 500px;
        margin-top: 0.25rem;
        margin-bottom: 0.5rem;
    }

    .submenu .nav-link {
        font-size: 0.825rem;
        padding: 0.45rem 1rem;
        margin: 0.15rem 0 0.15rem 0.5rem;
        border-radius: 8px;
        color: rgba(255, 255, 255, 0.6) !important;
        background: transparent !important;
    }
    
    .submenu .nav-link:hover,
    .submenu .nav-link.active {
        color: #FFFFFF !important;
        background: rgba(255, 255, 255, 0.06) !important;
        font-weight: 600;
        transform: translateX(2px);
    }

    .sidebar-menu .arrow {
        margin-left: auto;
        font-size: 0.75rem;
        opacity: 0.7;
        transition: transform .3s ease;
    }

    .has-submenu.open .arrow {
        transform: rotate(180deg);
        opacity: 1;
    }
</style>

{{-- ================= Script ================= --}}
<script>
    document.querySelectorAll('.submenu-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            this.closest('.has-submenu').classList.toggle('open');
        });
    });
</script>
