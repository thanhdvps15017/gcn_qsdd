@extends('welcome')

@section('title', 'Quản lý loại thủ tục')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-file-earmark-text me-2"></i>DANH SÁCH LOẠI THỦ TỤC
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
                                <th>TÊN THỦ TỤC</th>
                                <th class="text-center d-none d-md-table-cell">NGÀY TRẢ KẾT QUẢ</th>
                                <th width="5%" class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $item->name }}</td>
                                    <td class="text-center d-none d-md-table-cell">
                                        @if ($item->ngay_tra_ket_qua)
                                            <span class="badge bg-success">
                                                {{ $item->ngay_tra_ket_qua }} ngày
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="text-end position-static">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-2" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <button class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                    onclick="openEditModal(
                                                    {{ $item->id }},
                                                    '{{ addslashes($item->name) }}',
                                                    '{{ $item->ngay_tra_ket_qua ?? '' }}'
                                                )">
                                                    <i class="bi bi-pencil-square"></i>
                                                    Chỉnh sửa
                                                </button>

                                                <form action="{{ route('loai-thu-tuc.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('Xoá thủ tục «{{ addslashes($item->name) }}» ?')">
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
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Chưa có loại thủ tục nào
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
    <div class="modal fade" id="thuTucModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="fw-bold" id="modalTitle">Thêm loại thủ tục</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="thuTucForm" method="POST">
                    @csrf
                    <input type="hidden" id="methodField">

                    <div class="modal-body row g-3 p-4">
                        <div class="col-12">
                            <label class="fw-bold">Tên thủ tục *</label>
                            <input type="text" name="name" id="nameInput" class="form-control" required
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="fw-bold">Số ngày trả kết quả</label>
                            <input type="number" name="ngay_tra_ket_qua" id="daysInput" class="form-control"
                                value="{{ old('ngay_tra_ket_qua') }}" min="1" max="365">
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
        const modal = new bootstrap.Modal(document.getElementById('thuTucModal'));
        const form = document.getElementById('thuTucForm');

        function openAddModal() {
            form.reset();
            form.action = "{{ route('loai-thu-tuc.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('modalTitle').innerText = 'Thêm loại thủ tục';
            modal.show();
        }

        function openEditModal(id, name, days) {
            form.reset();
            form.action = `/settings/loai-thu-tuc/${id}`;
            document.getElementById('methodField').innerHTML =
                '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('modalTitle').innerText = 'Sửa loại thủ tục';
            document.getElementById('nameInput').value = name;
            document.getElementById('daysInput').value = days;
            modal.show();
        }

        // Mở lại modal nếu lỗi validate
        document.addEventListener('DOMContentLoaded', () => {
            @if ($errors->any())
                openAddModal();
            @endif
        });
    </script>
@endpush
