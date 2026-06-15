@extends('welcome')

@section('title', 'Quản lý sổ theo dõi')

@section('content')
    <div class="card-header text-white d-flex justify-content-between align-items-center mb-3 rounded-1"
        style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
        <h5 class="mb-0 fw-bold">Quản lý sổ theo dõi</h5>

        <button class="btn btn-light btn-sm d-inline-flex align-items-center gap-2" onclick="openAddModal()">
            <i class="bi bi-plus-lg"></i> Tạo sổ mới
        </button>
    </div>

    <div class="row g-4">
        @forelse ($groups as $group)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $group->book_code }} - {{ $group->book_name }}</h5>
                        <p class="card-text text-muted">{{ $group->description ?? 'Không có mô tả' }}</p>
                        <p class="card-text"><strong>{{ $group->ho_sos_count }}</strong> hồ sơ</p>
                        <p class="card-text small text-muted">
                            Tạo bởi: {{ optional($group->nguoiTao)->name ?? 'Hệ thống' }}<br>
                            Ngày tạo: {{ $group->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="card-footer bg-light d-flex gap-2">
                        <a href="{{ route('so-theo-doi.show', $group) }}"
                            class="btn btn-outline-primary btn-sm flex-fill rounded-1">
                            <i class="bi bi-eye"></i> Xem
                        </a>

                        <button class="btn btn-outline-warning btn-sm flex-fill rounded-1"
                            onclick="openEditModal({{ $group->id }}, '{{ addslashes($group->book_name) }}', '{{ addslashes($group->description ?? '') }}')">
                            <i class="bi bi-pencil"></i> Sửa
                        </button>

                        <form action="{{ route('so-theo-doi.destroy', $group) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-1"
                                onclick="confirmDelete(event, this.closest('form'), 'Bạn chắc chắn muốn xóa sổ theo dõi này?')">
                                <i class="bi bi-trash"></i> Xóa
                            </button>
                        </form>

                        <a href="{{ route('so-theo-doi.export-excel', $group) }}"
                            class="btn btn-success btn-sm rounded-1">
                            <i class="bi bi-file-earmark-excel"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                Chưa có sổ theo dõi nào. Hãy tạo mới!
            </div>
        @endforelse
    </div>

    {{ $groups->links() }}

    <!-- ================= MODAL TẠO / SỬA ================= -->
    <div class="modal fade" id="soTheoDoiModal" tabindex="-1" aria-labelledby="soTheoDoiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tạo sổ theo dõi mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="soForm" method="POST">
                    @csrf
                    <input type="hidden" id="methodField">

                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sổ <span class="text-danger">*</span></label>
                            <input type="text" name="book_name" id="tenSo" class="form-control" required
                                value="{{ old('book_name') }}">
                            @error('book_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" id="moTa" class="form-control" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-save me-1"></i> Tạo sổ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Đảm bảo các biến và hàm có scope global
        window.soModal = null;
        window.soForm = null;
        window.modalTitle = null;
        window.submitBtn = null;
        window.methodField = null;
        window.tenSoInput = null;
        window.moTaInput = null;

        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('soTheoDoiModal');
            if (!modalElement) {
                console.error('Modal element not found!');
                return;
            }

            window.soModal = new bootstrap.Modal(modalElement);
            window.soForm = document.getElementById('soForm');
            window.modalTitle = document.getElementById('modalTitle');
            window.submitBtn = document.getElementById('submitBtn');
            window.methodField = document.getElementById('methodField');
            window.tenSoInput = document.getElementById('tenSo');
            window.moTaInput = document.getElementById('moTa');

            @if ($errors->any())
                window.openAddModal();
            @endif
        });

        window.openAddModal = function() {
            if (!window.soModal) return;

            window.soForm.reset();
            window.soForm.action = "{{ route('so-theo-doi.store') }}";
            window.methodField.innerHTML = '';
            window.modalTitle.textContent = 'Tạo sổ theo dõi mới';
            window.submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Tạo sổ';

            window.tenSoInput.value = "{{ old('book_name') }}";
            window.moTaInput.value = "{{ old('description') }}";

            window.soModal.show();
        };

        window.openEditModal = function(id, book_name, description) {
            if (!window.soModal) return;

            window.soForm.action = `/so-theo-doi/${id}`;
            window.methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            window.modalTitle.textContent = 'Sửa sổ theo dõi';
            window.submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Cập nhật';

            window.tenSoInput.value = book_name;
            window.moTaInput.value = description || '';

            window.soModal.show();
        };
    </script>
@endsection
