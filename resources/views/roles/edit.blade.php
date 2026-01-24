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

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        {{-- ROLE NAME --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tên Role</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $role->name) }}" required>

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
                            @foreach ($permissions as $permission)
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input permission-item" type="checkbox" name="permissions[]"
                                            id="perm_{{ $permission->id }}" value="{{ $permission->name }}"
                                            @checked(in_array($permission->name, $rolePermissions))>

                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- ACTION --}}
                        <div class="mt-4 d-flex justify-content-between flex-column flex-md-row gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
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
