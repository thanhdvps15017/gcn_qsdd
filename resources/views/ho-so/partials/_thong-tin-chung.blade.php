<div class="row">

    <!-- CỘT TRÁI -->
    <div class="col-lg-6">
        <div class="card mb-3 shadow-sm">
            <div class="card-header fw-bold bg-light">Thông tin chung</div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Mã hồ sơ</label>
                        <input name="ma_ho_so" class="form-control" required
                            value="{{ $isEdit ? $getValue('ma_ho_so') : 'H19.151-' . $getValue('ma_ho_so') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tên chủ hồ sơ</label>
                        <input name="ten_chu_ho_so" class="form-control" placeholder="Họ và tên"
                            value="{{ $getValue('ten_chu_ho_so') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">SĐT chủ hồ sơ</label>
                        <input name="sdt_chu_ho_so" class="form-control" placeholder="SĐT chủ hồ sơ"
                            value="{{ $getValue('sdt_chu_ho_so') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Loại hồ sơ</label>
                        <select name="loai_ho_so_id" class="form-select">
                            @foreach ($loaiHoSos as $l)
                                <option value="{{ $l->id }}"
                                    {{ old('loai_ho_so_id', $isEdit ? $hoSo->loai_ho_so_id : '') == $l->id ? 'selected' : '' }}>
                                    {{ $l->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Loại thủ tục</label>
                        <select name="loai_thu_tuc_id" class="form-select" id="thoi_han" onchange="tinhHanTra(this)">
                            @foreach ($loaiThuTucs as $l)
                                <option value="{{ $l->id }}" data-days="{{ $l->ngay_tra_ket_qua }}"
                                    {{ old('loai_thu_tuc_id', $isEdit ? $hoSo->loai_thu_tuc_id : '') == $l->id ? 'selected' : '' }}>
                                    {{ $l->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Ngày trả kết quả</label>
                        <input type="date" name="han_giai_quyet" id="han_giai_quyet" class="form-control" readonly
                            value="{{ old('han_giai_quyet', $isEdit ? optional($hoSo->han_giai_quyet)->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hành chính công (Xã)</label>
                        <select name="xa_id" class="form-select">
                            @foreach ($xas as $x)
                                <option value="{{ $x->id }}"
                                    {{ old('xa_id', $isEdit ? $hoSo->xa_id : '') == $x->id ? 'selected' : '' }}>
                                    {{ $x->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Người thẩm tra</label>
                        <select name="nguoi_tham_tra_id" class="form-select">
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('nguoi_tham_tra_id', $isEdit ? $hoSo->nguoi_tham_tra_id : '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI -->
    <div class="col-lg-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header fw-bold bg-light">Ghi chú & Tài liệu</div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="ghi_chu" class="form-control" rows="1">{{ old('ghi_chu', $isEdit ? $hoSo->ghi_chu : '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tài liệu đính kèm</label>
                    <input type="file" name="files[]" class="form-control" multiple>
                    <small class="text-muted d-block mt-1">PDF, Word, ảnh, tối đa 10MB/file</small>
                </div>

                @if ($isEdit && $hoSo->files->count())

                    <div class="file-scroll mt-3">
                        @foreach ($hoSo->files as $file)
                            <div class="file-card" id="file-row-{{ $file->id }}">

                                <!-- ICON XOÁ -->
                                <button type="button" class="file-delete btn-delete-file"
                                    data-url="{{ route('ho-so.files.destroy', [$hoSo, $file]) }}"
                                    data-id="{{ $file->id }}" title="Xóa file">
                                    ✕
                                </button>

                                <!-- FILE NAME -->
                                <a href="{{ asset('storage/' . $file->duong_dan) }}" target="_blank"
                                    class="file-name text-primary text-decoration-none" title="{{ $file->ten_file }}">
                                    {{ $file->ten_file }}
                                </a>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted fst-italic mt-3 mb-3">
                        Chưa có file đính kèm
                    </div>

                @endif
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
