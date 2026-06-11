@extends('welcome')

@section('title', 'Sửa Role - ' . $role->name)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">
                        Sửa Role: {{ $role->name }}
                    </h5>
                </div>

                {{-- BODY --}}
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        {{-- ROLE NAME --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tên Role</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $role->name) }}" placeholder="Nhập tên Role">

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <hr>

                        {{-- PERMISSIONS --}}
                        <h5 class="fw-bold mb-3">Permissions</h5>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label fw-bold" for="selectAll">
                                Chọn tất cả
                            </label>
                        </div>

                        <div class="row g-2">
                            @php
                                $groupedPermissions = [];
                                foreach($permissions as $perm) {
                                    $parts = explode('.', $perm->name);
                                    $prefix = count($parts) > 1 ? $parts[0] : 'Khác';
                                    $groupedPermissions[$prefix][] = $perm;
                                }

                                $friendlyNames = [
                                    'settings.roles.index' => 'Phân quyền',
                                    'settings.users.index' => 'Tài khoản',
                                    'settings.loai-ho-so.index' => 'Loại hồ sơ',
                                    'settings.loai-thu-tuc.index' => 'Loại thủ tục',
                                    'settings.xa.index' => 'Xã - phường',
                                    'settings.mau-word.index' => 'Template Word',
                                    'settings.login-bg.edit' => 'Cài đặt chung',
                                    'ho-so.index' => 'Quản lý hồ sơ',
                                    'ho-so.create' => 'Thêm hồ sơ',
                                    'so-theo-doi.index' => 'Sổ theo dõi',
                                    'xuat-excel.index' => 'Xuất Excel',
                                    'xuat-word.index' => 'Xuất Word',
                                    'dashboard' => 'Dashboard',
                                ];
                            @endphp

                            @foreach($groupedPermissions as $prefix => $perms)
                                <div class="col-12 mt-3">
                                    <h6 class="fw-bold text-uppercase text-primary border-bottom pb-2 mb-3" style="color: var(--primary) !important;">
                                        <i class="bi bi-box-fill me-1"></i> Module: {{ $prefix }}
                                    </h6>
                                </div>
                                @foreach($perms as $permission)
                                    <div class="col-12 col-sm-6 col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input permission-item" type="checkbox" name="permissions[]"
                                                id="perm_{{ $permission->id }}" value="{{ $permission->name }}"
                                                @checked(in_array($permission->name, $rolePermissions))>
    
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $friendlyNames[$permission->name] ?? $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>

                        {{-- ACTION --}}
                        <div class="mt-4 d-flex justify-content-between flex-column flex-md-row gap-2">
                            <a href="{{ route('settings.roles.index') }}" class="btn btn-secondary">
                                Quay lại
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Lưu thay đổi
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAll');
            const items = document.querySelectorAll('.permission-item');

            const syncSelectAll = () => {
                selectAll.checked = [...items].every(i => i.checked);
            };

            selectAll.addEventListener('change', () => {
                items.forEach(i => i.checked = selectAll.checked);
            });

            items.forEach(i => {
                i.addEventListener('change', syncSelectAll);
            });

            // initial state
            syncSelectAll();
        });
    </script>
@endsection
