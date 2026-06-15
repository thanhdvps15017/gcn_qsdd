<div class="row">
    <div class="col-lg-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-header fw-bold bg-light">Thông tin chung & Tài liệu đính kèm</div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Mã hồ sơ <span class="text-danger">*</span></label>
                        <input name="dossier_code" class="form-control" required
                            value="{{ $isEdit ? $getValue('dossier_code') : 'H19.151-' . $getValue('dossier_code') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tên chủ hồ sơ</label>
                        <input name="owner_name" class="form-control" placeholder="Họ và tên"
                            value="{{ $getValue('owner_name') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">SĐT chủ hồ sơ</label>
                        <input name="owner_phone" class="form-control" placeholder="SĐT chủ hồ sơ"
                            value="{{ $getValue('owner_phone') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Loại hồ sơ</label>
                        <select name="dossier_type_id" class="form-select">
                            @foreach ($loaiHoSos as $l)
                                <option value="{{ $l->id }}"
                                    {{ old('dossier_type_id', $isEdit ? $hoSo->dossier_type_id : '') == $l->id ? 'selected' : '' }}>
                                    {{ $l->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Loại thủ tục</label>
                        <select name="procedure_type_id" class="form-select" id="thoi_han" onchange="tinhHanTra(this)">
                            @foreach ($loaiThuTucs as $l)
                                <option value="{{ $l->id }}" data-days="{{ $l->processing_days }}"
                                    {{ old('procedure_type_id', $isEdit ? $hoSo->procedure_type_id : '') == $l->id ? 'selected' : '' }}>
                                    {{ $l->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Ngày trả kết quả</label>
                        <input type="date" name="deadline" id="deadline" class="form-control" readonly
                            value="{{ old('deadline', $isEdit ? optional($hoSo->deadline)->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hành chính công (Xã)</label>
                        <select name="ward_id" class="form-select">
                            @foreach ($xas as $x)
                                <option value="{{ $x->id }}"
                                    {{ old('ward_id', $isEdit ? $hoSo->ward_id : '') == $x->id ? 'selected' : '' }}>
                                    {{ $x->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Người thẩm tra</label>
                        <select name="inspector_id" class="form-select">
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('inspector_id', $isEdit ? $hoSo->inspector_id : '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Ghi chú</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Nhập ghi chú chi tiết về hồ sơ...">{{ old('notes', $isEdit ? $hoSo->notes : '') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Tài liệu đính kèm</label>
                        <input type="file" name="files[]" class="form-control" multiple>
                        <small class="text-muted d-block mt-1">PDF, Word, ảnh, tối đa 10MB/file</small>
                    </div>

                    @if ($isEdit && $hoSo->files->count())
                        <div class="col-12 mt-2">
                            <label class="form-label fw-bold text-secondary small">Tài liệu hiện có</label>
                            <div class="file-scroll">
                                @foreach ($hoSo->files as $file)
                                    <div class="file-card" id="file-row-{{ $file->id }}">

                                        <!-- ICON XOÁ -->
                                        <button type="button" class="file-delete btn-delete-file"
                                            data-url="{{ route('ho-so.files.destroy', [$hoSo, $file]) }}"
                                            data-id="{{ $file->id }}" title="Xóa file">
                                            ✕
                                        </button>

                                        <!-- FILE NAME -->
                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                            class="file-name text-primary text-decoration-none" title="{{ $file->file_name }}">
                                            {{ $file->file_name }}
                                        </a>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .file-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 8px;
    }

    .file-card {
        min-width: 200px;
        max-width: 200px;
        flex-shrink: 0;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 6px;
        background: #f9f9f9;
        position: relative;
        transition: 0.2s ease;
    }

    .file-card:hover {
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
    }

    .file-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        font-size: 14px;
        padding-right: 20px;
    }

    .file-delete {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: none;
        background: #dc3545;
        color: #fff;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.15s ease;
    }

    .file-delete:hover {
        background: #bb2d3b;
        transform: scale(1.1);
    }
</style>
