<div class="row">
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
                        <input name="sdt_chu_ho_so" class="form-control" value="{{ $getValue('sdt_chu_ho_so') }}"
                            placeholder="SĐT chủ hồ sơ">
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

    <div class="col-lg-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header fw-bold bg-light">Ghi chú & Tài liệu</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="ghi_chu" class="form-control" rows="3">{{ old('ghi_chu', $isEdit ? $hoSo->ghi_chu : '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tài liệu đính kèm</label>
                    <input type="file" name="files[]" class="form-control" multiple>
                    <small class="text-muted d-block mt-1">PDF, Word, ảnh, tối đa 10MB/file</small>
                </div>

                @if ($isEdit && $hoSo->files->count())
                    <hr class="my-4">
                    <strong>File đã upload:</strong>
                    <ul class="list-unstyled mt-3">
                        @foreach ($hoSo->files as $file)
                            <li class="d-flex justify-content-between align-items-center py-2 border-bottom"
                                id="file-row-{{ $file->id }}">
                                <a href="{{ asset('storage/' . $file->duong_dan) }}" target="_blank"
                                    class="text-primary">
                                    {{ $file->ten_file }}
                                </a>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-file"
                                    data-url="{{ route('ho-so.files.destroy', [$hoSo, $file]) }}"
                                    data-id="{{ $file->id }}">Xóa</button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
