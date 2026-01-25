@extends('welcome')

@section('title', 'Quản lý mẫu Word')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">QUẢN LÝ MẪU WORD</h5>

                    <button class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#uploadForm">
                        <i class="bi bi-upload"></i> Upload mẫu
                    </button>
                </div>

                {{-- FORM UPLOAD --}}
                <div class="collapse show" id="uploadForm">
                    <div class="card-body border-bottom">
                        <form method="POST" action="{{ route('mau-word.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="fw-bold">Tên mẫu *</label>
                                    <input name="ten" class="form-control" value="{{ old('ten') }}" required>
                                </div>

                                <div class="col-md-5">
                                    <label class="fw-bold">File Word *</label>
                                    <input type="file" name="file" class="form-control" required>
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-primary w-100">
                                        <i class="bi bi-cloud-arrow-up"></i> Upload
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive p-3 overflow-visible">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>TÊN MẪU</th>
                                <th width="5%" class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mauWords as $mau)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $mau->ten }}</td>
                                    <td class="text-end position-static">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-2" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <button class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                    onclick="openEditMauWord({{ $mau->id }}, '{{ addslashes($mau->ten) }}')">
                                                    <i class="bi bi-pencil-square"></i>
                                                    Chỉnh sửa tên
                                                </button>

                                                <form action="{{ route('mau-word.destroy', $mau) }}" method="POST"
                                                    onsubmit="return confirm('Xoá mẫu {{ $mau->ten }}?')">
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
                                        Chưa có mẫu Word nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="mauWordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="fw-bold mb-0">Cập nhật tên mẫu Word</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="mauWordForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body p-4">
                        <label class="fw-bold">Tên mẫu *</label>
                        <input name="ten" id="mauWordName" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                        <button class="btn btn-primary">Lưu</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        let mauWordModal;
        let mauWordForm;
    
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap JS chưa được load!');
                return;
            }
    
            mauWordModal = new bootstrap.Modal(document.getElementById('mauWordModal'));
            mauWordForm  = document.getElementById('mauWordForm');
        });
    
        function openEditMauWord(id, ten) {
            if (!mauWordForm || !mauWordModal) {
                console.error('Modal chưa sẵn sàng');
                return;
            }
    
            mauWordForm.action = `/settings/mau-word/${id}`;
            document.getElementById('mauWordName').value = ten;
            mauWordModal.show();
        }
    </script>
@endsection
