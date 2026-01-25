@extends('welcome')

@section('title', 'Quản lý Role & Permission')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">DANH SÁCH ROLE & PERMISSION</h5>

                    <button class="btn btn-light btn-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal"
                        data-bs-target="#roleModal">
                        <i class="bi bi-plus-lg"></i>
                        Thêm mới
                    </button>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive p-3 overflow-visible">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="10%">#</th>
                                <th>TÊN ROLE</th>
                                <th width="10%" class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td class="text-muted">{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $role->name }}</td>

                                    {{-- ACTION --}}
                                    <td class="text-end position-static">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-2" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <a href="{{ route('roles.edit', $role) }}"
                                                    class="dropdown-item d-flex align-items-center gap-2 text-warning">
                                                    <i class="bi bi-pencil-square"></i>
                                                    Sửa
                                                </a>

                                                <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                                    onsubmit="return confirm('Bạn chắc chắn muốn xoá role này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                        <i class="bi bi-trash"></i>
                                                        Xoá
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        Chưa có role nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="roleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow rounded-3">

                <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="modal-title fw-bold" id="modalTitle">
                        Thêm Role mới
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <form id="roleForm" method="POST" action="{{ route('roles.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tên Role</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary">
                                <i class="bi bi-save"></i>
                                Lưu
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
