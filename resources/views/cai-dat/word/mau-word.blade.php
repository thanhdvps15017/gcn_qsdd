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

                    <button class="btn btn-light btn-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal"
                        data-bs-target="#createFolderModal">
                        <i class="bi bi-plus-lg"></i>
                        Thêm thư mục mới
                    </button>
                </div>

                {{-- BODY --}}
                <div class="card-body p-4 bg-light">
                    <!-- Danh sách thư mục -->
                    <div class="row g-4">
            @forelse ($folders as $folder)
                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm h-100">
                        <!-- Header thư mục -->
                        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h6 class="mb-0 fw-bold text-truncate" style="max-width: 60%;">
                                <i class="bi bi-folder-fill text-warning me-2"></i>
                                {{ $folder->name }}
                            </h6>
                            <div class="d-flex gap-2 flex-nowrap">
                                <!-- NÚT SỬA THƯ MỤC -->
                                <button class="btn btn-sm btn-outline-warning btn-edit-folder" data-id="{{ $folder->id }}"
                                    data-ten="{{ addslashes($folder->name) }}">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </button>

                                <form method="POST" action="{{ route('settings.mau-word.destroy-folder', $folder) }}"
                                    onsubmit="confirmDelete(event, this, 'Xóa thư mục này sẽ xóa hết mẫu bên trong. Tiếp tục?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Upload mẫu mới -->
                        <div class="card-body border-bottom">
                            <form method="POST" action="{{ route('settings.mau-word.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="action" value="upload_template">
                                <input type="hidden" name="folder_id" value="{{ $folder->id }}">

                                <div class="mb-3">
                                    <input name='name' class="form-control" placeholder="Tên mẫu Word" value="{{ old('name') }}">
                                    @error('name')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">File Word (.doc, .docx)</label>
                                    <input type="file" name="file" class="form-control" accept=".doc,.docx">
                                    @error('file')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Ghi chú</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">File đính kèm</label>
                                    <input type="file" name="attachment" class="form-control"
                                        accept=".pdf,.docx,.xlsx,.jpg,.png,.zip">
                                </div>
                                <button class="btn btn-primary w-100 btn-sm">Upload mẫu</button>
                            </form>
                        </div>

                        <!-- Danh sách mẫu -->
                        @if ($folder->mauWords->isNotEmpty())
                            <ul class="list-group list-group-flush">
                                @foreach ($folder->mauWords as $mau)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div class="flex-grow-1">
                                                <strong>
                                                    <i class="bi bi-file-earmark-word-fill text-primary me-1"></i>
                                                    {{ $mau->name }}
                                                </strong>
                                                @if ($mau->notes)
                                                    <div class="small text-secondary mt-1">
                                                        {{ Str::limit($mau->notes, 80) }}
                                                    </div>
                                                @endif
                                                @if ($mau->attachment)
                                                    <div class="small mt-1">
                                                        <a href="{{ Storage::url($mau->attachment) }}" target="_blank"
                                                            class="text-info">
                                                            <i class="bi bi-paperclip"></i> File đính kèm
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- NÚT SỬA + XÓA MẪU WORD -->
                                            <div class="d-flex gap-2 flex-nowrap">
                                                <button class="btn btn-sm btn-outline-warning btn-edit-mau"
                                                    data-id="{{ $mau->id }}" data-ten="{{ addslashes($mau->name) }}"
                                                    data-ghichu="{{ addslashes($mau->notes ?? '') }}"
                                                    data-folder-id="{{ $mau->folder_id }}">
                                                    <i class="bi bi-pencil-square"></i> Sửa
                                                </button>

                                                <form method="POST" action="{{ route('settings.mau-word.destroy', $mau) }}"
                                                    onsubmit="confirmDelete(event, this, 'Xóa mẫu Word này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                                        <i class="bi bi-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center text-muted py-3">Chưa có mẫu Word nào</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    Chưa có thư mục nào. Hãy tạo thư mục đầu tiên.
                </div>
            @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === Modal tạo thư mục === -->
    <div class="modal fade" id="createFolderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="modal-title fw-bold">Tạo thư mục mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('settings.mau-word.store') }}">
                    @csrf
                    <input type="hidden" name="action" value="create_folder">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên thư mục *</label>
                            <input type="text" name='name' class="form-control" placeholder="Nhập tên thư mục" value="{{ old('name') }}">
                            @error('name')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Tạo thư mục</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- === Modal sửa thư mục === -->
    <div class="modal fade" id="editFolderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="modal-title fw-bold">Chỉnh sửa thư mục</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                    <form method="POST" id="editFolderForm">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="type" value="folder">
                            <input type="hidden" name="id" id="folder_id_hidden">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên thư mục</label>
                                <input type="text" name='name' id="folder_ten" class="form-control" value="{{ old('name') }}" placeholder="Nhập tên thư mục">
                                @error('name')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <!-- === Modal sửa mẫu Word === -->
    <div class="modal fade" id="editMauModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="modal-title fw-bold">Chỉnh sửa mẫu Word</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                    <form method="POST" id="editMauForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="type" value="mauword">
                            <input type="hidden" name="id" id="mau_id_hidden">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tên mẫu</label>
                                    <input type="text" name='name' id="mau_ten" class="form-control" value="{{ old('name') }}" placeholder="Nhập tên mẫu Word">
                                    @error('name')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thư mục</label>
                                    <select name="folder_id" id="mau_folder_id" class="form-select" data-container="body">
                                        @foreach ($folders as $f)
                                            <option value="{{ $f->id }}" @selected(old('folder_id') == $f->id)>{{ $f->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('folder_id')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Ghi chú</label>
                                    <textarea name="notes" id="mau_ghi_chu" class="form-control" rows="3" placeholder="Nhập ghi chú..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thay file Word</label>
                                    <input type="file" name="file" class="form-control" accept=".doc,.docx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thay file đính kèm</label>
                                    <input type="file" name="attachment" class="form-control"
                                        accept=".pdf,.docx,.xlsx,.jpg,.png,.zip">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Sửa thư mục
            document.querySelectorAll('.btn-edit-folder').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('folder_id_hidden').value = this.dataset.id;
                    document.getElementById('folder_ten').value = this.dataset.ten;

                    // Sửa route để khớp với route đã khai báo
                    document.getElementById('editFolderForm').action =
                        '{{ route('settings.mau-word.update', '') }}' + '/' + this.dataset.id;

                    new bootstrap.Modal(document.getElementById('editFolderModal')).show();
                });
            });

            // Sửa mẫu Word
            document.querySelectorAll('.btn-edit-mau').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('mau_id_hidden').value = this.dataset.id;
                    document.getElementById('mau_ten').value = this.dataset.ten;
                    document.getElementById('mau_ghi_chu').value = this.dataset.ghichu || '';
                    $('#mau_folder_id').val(this.dataset.folderId).selectpicker('refresh');

                    // Sửa route để khớp
                    document.getElementById('editMauForm').action =
                        '{{ route('settings.mau-word.update', '') }}' + '/' + this.dataset.id;

                    new bootstrap.Modal(document.getElementById('editMauModal')).show();
                });
            });

        });
        
        document.addEventListener('DOMContentLoaded', () => {
            @if ($errors->any())
                @if (old('action') == 'create_folder')
                    new bootstrap.Modal(document.getElementById('createFolderModal')).show();
                @elseif (old('type') == 'folder')
                    document.getElementById('editFolderForm').action = '{{ route('settings.mau-word.update', '') }}/{{ old('id') }}';
                    new bootstrap.Modal(document.getElementById('editFolderModal')).show();
                @elseif (old('type') == 'mauword')
                    document.getElementById('editMauForm').action = '{{ route('settings.mau-word.update', '') }}/{{ old('id') }}';
                    new bootstrap.Modal(document.getElementById('editMauModal')).show();
                @endif
            @endif
        });
    </script>
@endpush
