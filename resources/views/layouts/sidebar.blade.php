<aside class="sidebar" id="sidebar" onclick="event.stopPropagation()">
    <ul class="nav flex-column pt-3">

        {{-- Dashboard --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        {{-- Hồ sơ --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ho-so.create') ? 'active' : '' }}"
                href="{{ route('ho-so.create') }}">
                <i class="bi bi-plus-circle me-2"></i> Thêm hồ sơ
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ho-so.index') ? 'active' : '' }}"
                href="{{ route('ho-so.index') }}">
                <i class="bi bi-list-ul me-2"></i> Danh sách hồ sơ
            </a>
        </li>


        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('hso-theo-doi.index') ? 'active' : '' }}"
                href="{{ route('so-theo-doi.index') }}">
                <i class="bi bi-journal-bookmark"></i> Quản lý sổ theo dõi
            </a>
        </li>

        {{-- CÀI ĐẶT --}}
        @php
            $settingOpen = request()->routeIs('roles.*');
        @endphp

        <li class="nav-item has-submenu {{ $settingOpen ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-link d-flex justify-content-between align-items-center"
                onclick="toggleSubmenu(this)">
                <span>
                    <i class="bi bi-gear me-2"></i> Cài đặt
                </span>
                <i class="bi bi-chevron-down arrow"></i>
            </a>

            <ul class="submenu">
                <li>
                    <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}"
                        href="{{ route('roles.index') }}">
                        <i class="bi bi-shield-lock me-2"></i>
                        Phân quyền
                    </a>
                </li>

                <li>
                    <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <i class="bi bi-shield-lock me-2"></i>
                        Tài khoản
                    </a>
                </li>

                <li>
                    <a class="nav-link {{ request()->routeIs('loai-ho-so.index') ? 'active' : '' }}"
                        href="{{ route('loai-ho-so.index') }}">
                        <i class="bi bi-shield-lock me-2"></i>
                        Loại hồ sơ
                    </a>
                </li>

                <li>
                    <a class="nav-link {{ request()->routeIs('loai-thu-tuc.index') ? 'active' : '' }}"
                        href="{{ route('loai-thu-tuc.index') }}">
                        <i class="bi bi-shield-lock me-2"></i>
                        Loại thủ tục
                    </a>
                </li>

                <li>
                    <a class="nav-link {{ request()->routeIs('xa.index') ? 'active' : '' }}"
                        href="{{ route('xa.index') }}">
                        <i class="bi bi-shield-lock me-2"></i>
                        Xã - phường
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</aside>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>


@push('styles')
    <style>
        .has-submenu .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease;
            padding-left: 1.5rem;
        }

        .has-submenu.open .submenu {
            max-height: 300px;
        }

        .has-submenu .arrow {
            transition: transform .3s ease;
        }

        .has-submenu.open .arrow {
            transform: rotate(180deg);
        }
    </style>
@endpush


@push('script')
    <script>
        function toggleSubmenu(el) {
            el.closest('.has-submenu').classList.toggle('open');
        }
    </script>
@endpush
