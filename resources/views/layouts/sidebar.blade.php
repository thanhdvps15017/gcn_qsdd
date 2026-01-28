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
                'roles.*',
                'users.*',
                'loai-ho-so.*',
                'loai-thu-tuc.*',
                'xa.*',
                'mau-word.*',
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
                @can('roles.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                            href="{{ route('roles.index') }}">
                            Phân quyền
                        </a>
                    </li>
                @endcan

                @can('users.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                            Tài khoản
                        </a>
                    </li>
                @endcan

                @can('loai-ho-so.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('loai-ho-so.*') ? 'active' : '' }}"
                            href="{{ route('loai-ho-so.index') }}">
                            Loại hồ sơ
                        </a>
                    </li>
                @endcan

                @can('loai-thu-tuc.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('loai-thu-tuc.*') ? 'active' : '' }}"
                            href="{{ route('loai-thu-tuc.index') }}">
                            Loại thủ tục
                        </a>
                    </li>
                @endcan

                @can('xa.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('xa.*') ? 'active' : '' }}"
                            href="{{ route('xa.index') }}">
                            Xã - phường
                        </a>
                    </li>
                @endcan

                @can('mau-word.index')
                    <li>
                        <a class="nav-link {{ request()->routeIs('mau-word.*') ? 'active' : '' }}"
                            href="{{ route('mau-word.index') }}">
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
    .sidebar ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .has-submenu .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height .3s ease;
        padding-left: 1.5rem;
    }

    .has-submenu.open .submenu {
        max-height: 500px;
    }

    .submenu .nav-link {
        font-size: .9rem;
        padding: .5rem 1rem;
    }

    .sidebar-menu .nav-link {
        display: flex;
        align-items: center;
    }

    .sidebar-menu .arrow {
        margin-left: auto;
    }

    .arrow {
        transition: transform .3s ease;
    }

    .has-submenu.open .arrow {
        transform: rotate(180deg);
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
