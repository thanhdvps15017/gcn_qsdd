@extends('welcome')

@section('title', 'Quản lý loại hồ sơ')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-folder me-2"></i>DANH SÁCH LOẠI HỒ SƠ
                    </h5>

                    <button class="btn btn-light btn-sm" onclick="openAddModal()">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </button>
                </div>

                {{-- BODY --}}
                <div class="table-responsive p-3 overflow-visible">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>TÊN LOẠI HỒ SƠ</th>
                                <th width="5%" class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $item->name }}</td>

                                    <td class="text-end position-static">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-2" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <button class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                    onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                                    <i class="bi bi-pencil-square"></i>
                                                    <span>Chỉnh sửa</span>
                                                </button>

                                                <form action="{{ route('loai-ho-so.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('Bạn chắc chắn muốn xoá loại hồ sơ «{{ addslashes($item->name) }}» ?')">
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
                                    <td colspan="3" class="text-center text-muted py-4">
                                        Chưa có loại hồ sơ nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL CREATE / EDIT ================= --}}
    <div class="modal fade" id="loaiHoSoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="fw-bold" id="modalTitle">Thêm loại hồ sơ</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="loaiHoSoForm" method="POST">
                    @csrf
                    <input type="hidden" id="methodField">

                    <div class="modal-body row g-3 p-4">

                        <div class="col-12">
                            <label class="fw-bold">Tên loại hồ sơ *</label>
                            <input type="text" name="name" id="nameInput" class="form-control" required
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        const loaiHoSoModal = new bootstrap.Modal(document.getElementById('loaiHoSoModal'));
        const form = document.getElementById('loaiHoSoForm');

        function openAddModal() {
            form.reset();
            form.action = "{{ route('loai-ho-so.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('modalTitle').innerText = 'Thêm loại hồ sơ';
            document.getElementById('nameInput').value = '';
            loaiHoSoModal.show();
        }

        function openEditModal(id, name) {
            form.reset();
            form.action = `/settings/loai-ho-so/${id}`;

            document.getElementById('modalTitle').innerText = 'Sửa loại hồ sơ';
            document.getElementById('methodField').innerHTML =
                '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('nameInput').value = name;

            loaiHoSoModal.show();
        }

        // Tự động mở modal khi có lỗi validate từ server
        document.addEventListener('DOMContentLoaded', () => {
            @if ($errors->any())
                openAddModal();  // hoặc có thể thêm logic để fill lại dữ liệu cũ nếu cần
            @endif
        });
    </script>
@endpush