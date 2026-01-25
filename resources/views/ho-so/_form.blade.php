@php
    // Giá trị mặc định nếu không truyền từ controller
    $title ??= 'THÊM MỚI HỒ SƠ';
    $action ??= route('ho-so.store');
    $method ??= 'POST';
    $submitText ??= 'Lưu hồ sơ';
    $hoSo ??= null;
    $isEdit = !is_null($hoSo);

    // Helper lấy giá trị: ưu tiên old() > model > rỗng
    $getValue = function ($key, $default = '') use ($isEdit, $hoSo) {
        return old($key, $isEdit ? data_get($hoSo, $key, $default) : $default);
    };

    $getArrayValue = function ($arrayKey, $subKey = null) use ($isEdit, $hoSo) {
        $value = old($arrayKey);
        if ($value !== null) {
            return $value;
        }
        if (!$isEdit) {
            return $subKey ? '' : [];
        }
        $data = data_get($hoSo, $arrayKey, []);
        return $subKey ? $data[$subKey] ?? '' : $data;
    };

    // Dữ liệu động
    $chuSuDung = $getArrayValue('chu_su_dung');
    $uyQuyen = $getArrayValue('uy_quyen');
    $thuaChung = old('thua_chung', $isEdit ? array_values((array) ($hoSo->thua_chung ?? [])) : []);
    $thongTinRieng = old('thong_tin_rieng', $isEdit ? $hoSo->thong_tin_rieng ?? [] : []);
    $riengLoai = $thongTinRieng['loai'] ?? '';
    $riengData = $thongTinRieng['data'] ?? [];
    $riengThua = array_values((array) ($riengData['thua'] ?? []));

    if (empty($thuaChung)) {
        $thuaChung = [['to' => '', 'thua' => '', 'dien_tich' => '']];
    }
@endphp

<form action="{{ $action }}" method="POST">
    @csrf

    @if (isset($method) && strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="card-header text-white d-flex justify-content-between align-items-center mb-3 rounded-1"
        style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
        <h5 class="mb-0 fw-bold">{{ $title }}</h5>
        <a href="{{ route('ho-so.index') }}" class="btn btn-light btn-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- THÔNG TIN CHUNG --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Thông tin chung</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Mã hồ sơ</label>
                    <div class="input-group">
                        <input name="ma_ho_so" class="form-control" required
                            value="{{ $isEdit ? $getValue('ma_ho_so') : 'H19.151-' . $getValue('ma_ho_so') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Tên chủ hồ sơ</label>
                    <div class="input-group">
                        <select name="xung_ho" class="form-select" style="max-width: 90px;">
                            <option value="Ông"
                                {{ old('xung_ho', $isEdit ? $hoSo->xung_ho ?? 'ong' : 'ong') === 'ong' ? 'selected' : '' }}>
                                Ông</option>
                            <option value="Bà"
                                {{ old('xung_ho', $isEdit ? $hoSo->xung_ho ?? 'ong' : 'ong') === 'ba' ? 'selected' : '' }}>
                                Bà</option>
                        </select>
                        <input name="ten_chu_ho_so" class="form-control" placeholder="Họ và tên"
                            value="{{ $getValue('ten_chu_ho_so') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <label>SĐT chủ hồ sơ</label>
                    <input name="sdt_chu_ho_so" class="form-control" value="{{ $getValue('sdt_chu_ho_so') }}">
                </div>
                <div class="col-md-6">
                    <label>Loại hồ sơ</label>
                    <select name="loai_ho_so_id" class="form-select">
                        <option value="">-- Chọn --</option>
                        @foreach ($loaiHoSos as $l)
                            <option value="{{ $l->id }}"
                                {{ old('loai_ho_so_id', $isEdit ? $hoSo->loai_ho_so_id : '') == $l->id ? 'selected' : '' }}>
                                {{ $l->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Loại thủ tục</label>
                    <select name="loai_thu_tuc_id" class="form-select" onchange="tinhHanTra(this)">
                        <option value="">-- Chọn --</option>
                        @foreach ($loaiThuTucs as $l)
                            <option value="{{ $l->id }}" data-days="{{ $l->ngay_tra_ket_qua }}"
                                {{ old('loai_thu_tuc_id', $isEdit ? $hoSo->loai_thu_tuc_id : '') == $l->id ? 'selected' : '' }}>
                                {{ $l->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Hạn trả kết quả</label>
                    <input type="date" name="han_giai_quyet" id="han_giai_quyet" class="form-control" readonly
                        value="{{ old('han_giai_quyet', $isEdit ? optional($hoSo->han_giai_quyet)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-6">
                    <label>Hành chính công (Xã)</label>
                    <select name="xa_id" class="form-select">
                        <option value="">-- Chọn --</option>
                        @foreach ($xas as $x)
                            <option value="{{ $x->id }}"
                                {{ old('xa_id', $isEdit ? $hoSo->xa_id : '') == $x->id ? 'selected' : '' }}>
                                {{ $x->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Người thẩm tra</label>
                    <select name="nguoi_tham_tra_id" class="form-select">
                        <option value="">-- Chọn --</option>
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

    {{-- CHỦ SỬ DỤNG --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Thông tin chủ sử dụng</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label>Họ tên</label>
                <input name="chu_su_dung[ho_ten]" class="form-control" value="{{ $chuSuDung['ho_ten'] ?? '' }}">
            </div>
            <div class="col-md-4">
                <label>CMND / CCCD</label>
                <input name="chu_su_dung[cccd]" class="form-control" value="{{ $chuSuDung['cccd'] ?? '' }}">
            </div>
            <div class="col-md-4">
                <label>Ngày cấp</label>
                <input type="date" name="chu_su_dung[ngay_cap]" class="form-control"
                    value="{{ $chuSuDung['ngay_cap'] ?? '' }}">
            </div>
            <div class="col-12">
                <label>Địa chỉ</label>
                <textarea name="chu_su_dung[dia_chi]" class="form-control">{{ $chuSuDung['dia_chi'] ?? '' }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse"
                    data-bs-target="#uyquyen">
                    Có người ủy quyền
                </button>
            </div>
            <div class="collapse mt-3 {{ !empty($uyQuyen) ? 'show' : '' }}" id="uyquyen">
                <div class="row g-3">
                    <div class="col-md-6">
                        <textarea name="uy_quyen[nguoi]" class="form-control" placeholder="Người ủy quyền">{{ $uyQuyen['nguoi'] ?? '' }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <textarea name="uy_quyen[giay]" class="form-control" placeholder="Giấy ủy quyền">{{ $uyQuyen['giay'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- THỬA - TỜ - DIỆN TÍCH (thửa chung) --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">Thửa - tờ - diện tích</span>
            <button type="button" class="btn btn-success btn-sm" onclick="addThua('tblThua')">+ Thêm</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="tblThua">
                <thead>
                    <tr>
                        <th>Tờ</th>
                        <th>Thửa</th>
                        <th>Diện tích</th>
                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($thuaChung as $idx => $row)
                        <tr>
                            <td><input name="thua_chung[{{ $idx }}][to]" class="form-control"
                                    value="{{ $row['to'] ?? '' }}"></td>
                            <td><input name="thua_chung[{{ $idx }}][thua]" class="form-control"
                                    value="{{ $row['thua'] ?? '' }}"></td>
                            <td><input name="thua_chung[{{ $idx }}][dien_tich]" class="form-control"
                                    value="{{ $row['dien_tich'] ?? '' }}"></td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="this.closest('tr').remove()">X</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label>Ngày cấp GCN</label>
                    <input type="date" name="ngay_cap_gcn" class="form-control"
                        value="{{ old('ngay_cap_gcn', $isEdit ? optional($hoSo->ngay_cap_gcn)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label>Số vào sổ</label>
                    <input name="so_vao_so" class="form-control" value="{{ $getValue('so_vao_so') }}">
                </div>
                <div class="col-md-4">
                    <label>Số phát hành</label>
                    <input name="so_phat_hanh" class="form-control" value="{{ $getValue('so_phat_hanh') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- THÔNG TIN RIÊNG - Chỉ 1 form duy nhất --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Thông tin riêng</div>
        <div class="card-body">

            <div class="mb-4">
                <label class="fw-bold">Loại thủ tục chi tiết</label>
                <select name="thong_tin_rieng[loai]" class="form-select">
                    <option value="">-- Chọn loại --</option>
                    <option value="tachthua_chuyennhuong"
                        {{ old('thong_tin_rieng.loai', $riengLoai) === 'tachthua_chuyennhuong' ? 'selected' : '' }}>
                        Tách thửa - chuyển nhượng</option>
                    <option value="capdoi"
                        {{ old('thong_tin_rieng.loai', $riengLoai) === 'capdoi' ? 'selected' : '' }}>Cấp đổi</option>
                    <option value="chuyennhuong"
                        {{ old('thong_tin_rieng.loai', $riengLoai) === 'chuyennhuong' ? 'selected' : '' }}>Chuyển
                        nhượng</option>
                    <option value="tachthua"
                        {{ old('thong_tin_rieng.loai', $riengLoai) === 'tachthua' ? 'selected' : '' }}>Tách thửa
                    </option>
                    <option value="capdoi_chuyennhuong"
                        {{ old('thong_tin_rieng.loai', $riengLoai) === 'capdoi_chuyennhuong' ? 'selected' : '' }}>Cấp
                        đổi + chuyển nhượng</option>
                </select>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label>Họ tên</label>
                    <input name="thong_tin_rieng[data][ho_ten]" class="form-control"
                        value="{{ old('thong_tin_rieng.data.ho_ten', $riengData['ho_ten'] ?? '') }}"
                        placeholder="Họ và tên người liên quan">
                </div>
                <div class="col-md-4">
                    <label>CMND / CCCD</label>
                    <input name="thong_tin_rieng[data][cccd]" class="form-control"
                        value="{{ old('thong_tin_rieng.data.cccd', $riengData['cccd'] ?? '') }}"
                        placeholder="Số CMND / CCCD">
                </div>
                <div class="col-md-4">
                    <label>Ngày cấp CCCD / CMND</label>
                    <input type="date" name="thong_tin_rieng[data][ngay_cap_cccd]" class="form-control"
                        value="{{ old('thong_tin_rieng.data.ngay_cap_cccd', $riengData['ngay_cap_cccd'] ?? '') }}">
                </div>
                <div class="col-12">
                    <label>Địa chỉ</label>
                    <textarea name="thong_tin_rieng[data][dia_chi]" class="form-control" rows="2"
                        placeholder="Địa chỉ thường trú / nơi ở hiện tại">{{ old('thong_tin_rieng.data.dia_chi', $riengData['dia_chi'] ?? '') }}</textarea>
                </div>
            </div>

            <h6 class="fw-bold mb-3">Danh sách thửa đất</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tờ</th>
                        <th>Thửa</th>
                        <th>Diện tích (m²)</th>
                        <th>Ghi chú</th>
                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody id="tbl_thongtinrieng">
                    @foreach ($riengThua as $idx => $t)
                        <tr>
                            <td><input name="thong_tin_rieng[data][thua][{{ $idx }}][to]"
                                    class="form-control" value="{{ $t['to'] ?? '' }}"></td>
                            <td><input name="thong_tin_rieng[data][thua][{{ $idx }}][thua]"
                                    class="form-control" value="{{ $t['thua'] ?? '' }}"></td>
                            <td><input name="thong_tin_rieng[data][thua][{{ $idx }}][dien_tich]"
                                    class="form-control" value="{{ $t['dien_tich'] ?? '' }}"></td>
                            <td><input name="thong_tin_rieng[data][thua][{{ $idx }}][ghi_chu]"
                                    class="form-control" value="{{ $t['ghi_chu'] ?? '' }}"></td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="this.closest('tr').remove()">X</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- <button type="button" class="btn btn-success btn-sm mt-2" onclick="addRiengRow('tbl_thongtinrieng')">+
                Thêm thửa</button> --}}

        </div>
    </div>

    {{-- GHI CHÚ --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Ghi chú</div>
        <div class="card-body">
            <textarea name="ghi_chu" class="form-control" rows="4">{{ old('ghi_chu', $isEdit ? $hoSo->ghi_chu : '') }}</textarea>
        </div>
    </div>

    <input type="hidden" name="trang_thai"
        value="{{ old('trang_thai', $isEdit ? $hoSo->trang_thai : 'dang_giai_quyet') }}">

    <div class="text-end">
        <button type="submit" class="btn btn-success px-5">{{ $submitText }}</button>
    </div>
</form>

<script>
    let thuaChungIndex = {{ count($thuaChung) }};
    let thongRiengThuaIndex = {{ count($riengThua) }};

    function tinhHanTra(select) {
        const days = select.options[select.selectedIndex]?.dataset.days;
        if (!days) return;
        const today = new Date();
        today.setDate(today.getDate() + parseInt(days, 10));
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        document.getElementById('han_giai_quyet').value = `${yyyy}-${mm}-${dd}`;
    }

    function addThua(tblId) {
        const i = thuaChungIndex++;
        document.querySelector(`#${tblId} tbody`).insertAdjacentHTML('beforeend', `
            <tr>
                <td><input name="thua_chung[${i}][to]" class="form-control"></td>
                <td><input name="thua_chung[${i}][thua]" class="form-control"></td>
                <td><input name="thua_chung[${i}][dien_tich]" class="form-control"></td>
                <td style="text-align: center;"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
            </tr>
        `);
    }

    function addRiengRow(tbodyId) {
        const i = thongRiengThuaIndex++;
        const html = `
            <tr>
                <td><input name="thong_tin_rieng[data][thua][${i}][to]" class="form-control"></td>
                <td><input name="thong_tin_rieng[data][thua][${i}][thua]" class="form-control"></td>
                <td><input name="thong_tin_rieng[data][thua][${i}][dien_tich]" class="form-control"></td>
                <td><input name="thong_tin_rieng[data][thua][${i}][ghi_chu]" class="form-control"></td>
                <td style="text-align: center;"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
            </tr>`;
        document.getElementById(tbodyId)?.insertAdjacentHTML('beforeend', html);
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (thuaChungIndex === 0) addThua('tblThua');
        if (thongRiengThuaIndex === 0) addRiengRow('tbl_thongtinrieng');

        const loaiSelect = document.querySelector('select[name="loai_thu_tuc_id"]');
        if (loaiSelect && loaiSelect.selectedIndex > 0) {
            tinhHanTra(loaiSelect);
        }
    });
</script>
