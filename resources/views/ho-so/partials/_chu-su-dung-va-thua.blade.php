<div class="row mb-5">
    <!-- CỘT TRÁI: CHỦ SỬ DỤNG -->
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <span class="fw-bold">Thông tin chủ sử dụng (Theo GCN)</span>
                <button type="button" class="btn btn-success btn-sm" onclick="addChuSuDung()">+ Thêm</button>
            </div>

            <div class="card-body" id="chuSuDungContainer">
                @foreach ($chuSuDungList as $idx => $chu)
                    <div class="border rounded p-3 bg-light position-relative chu-su-dung-item mb-3"
                        data-index="{{ $idx }}">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2"
                            onclick="removeChuSuDung(this)">X</button>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label>Họ tên <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="chu_su_dung[{{ $idx }}][xung_ho]" class="form-select"
                                        style="max-width: 90px;">
                                        <option value="Ông" {{ ($chu['xung_ho'] ?? '') == 'Ông' ? 'selected' : '' }}>
                                            Ông</option>
                                        <option value="Bà" {{ ($chu['xung_ho'] ?? '') == 'Bà' ? 'selected' : '' }}>
                                            Bà</option>
                                    </select>
                                    <input name="chu_su_dung[{{ $idx }}][ho_ten]" class="form-control"
                                        value="{{ old("chu_su_dung.$idx.ho_ten", $chu['ho_ten'] ?? '') }}"
                                        placeholder="Họ và tên" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Ngày sinh</label>
                                <input type="date" name="chu_su_dung[{{ $idx }}][ngay_sinh]"
                                    class="form-control"
                                    value="{{ old("chu_su_dung.$idx.ngay_sinh", $chu['ngay_sinh'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label>CCCD/CMND</label>
                                <input name="chu_su_dung[{{ $idx }}][cccd]" class="form-control"
                                    value="{{ old("chu_su_dung.$idx.cccd", $chu['cccd'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label>Ngày cấp</label>
                                <input type="date" name="chu_su_dung[{{ $idx }}][ngay_cap]"
                                    class="form-control"
                                    value="{{ old("chu_su_dung.$idx.ngay_cap", $chu['ngay_cap'] ?? '') }}">
                            </div>
                            <div class="col-12">
                                <label>Địa chỉ</label>
                                <textarea name="chu_su_dung[{{ $idx }}][dia_chi]" class="form-control" rows="2">
                                    {{ old("chu_su_dung.$idx.dia_chi", $chu['dia_chi'] ?? '') }}
                                </textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer bg-light border-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="uy_quyen[nguoi]" class="form-control" placeholder="Người ủy quyền"
                            value="{{ old('uy_quyen.nguoi', $uyQuyen['nguoi'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="uy_quyen[giay]" class="form-control" placeholder="Giấy ủy quyền"
                            value="{{ old('uy_quyen.giay', $uyQuyen['giay'] ?? '') }}">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- CỘT PHẢI: THỬA ĐẤT -->
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <span class="fw-bold">Thửa - tờ - diện tích - địa chỉ thửa đất</span>
                <button type="button" class="btn btn-success btn-sm" onclick="addThua()">+ Thêm</button>
            </div>

            <div class="card-body" id="thuaContainer">
                @foreach ($thuaChung as $idx => $row)
                    <div class="border rounded p-3 mb-3 bg-light position-relative thua-item"
                        data-index="{{ $idx }}">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2"
                            onclick="removeThua(this)">X</button>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label>Tờ</label>
                                <input name="thua_chung[{{ $idx }}][to]" class="form-control"
                                    value="{{ $row['to'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label>Thửa</label>
                                <input name="thua_chung[{{ $idx }}][thua]" class="form-control"
                                    value="{{ $row['thua'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label>Diện tích (m²)</label>
                                <input name="thua_chung[{{ $idx }}][dien_tich]" class="form-control"
                                    value="{{ $row['dien_tich'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label>Xã</label>
                                <select name="thua_chung[{{ $idx }}][xa_id]" class="form-select">
                                    @foreach ($xas as $x)
                                        <option value="{{ $x->id }}"
                                            {{ ($row['xa_id'] ?? '') == $x->id ? 'selected' : '' }}>
                                            {{ $x->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label>Ấp / Thôn</label>
                                <input name="thua_chung[{{ $idx }}][ap_thon]" class="form-control"
                                    value="{{ $row['ap_thon'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer bg-light border-0">
                <div class="row g-3">
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
    </div>
</div>

<!-- JavaScript dành riêng cho phần Chủ sử dụng & Thửa đất -->
<script>
    let chuSuDungIndex = {{ $chuSuDungIndex }};
    let thuaChungIndex = {{ count($thuaChung) }};

    function addChuSuDung() {
        const container = document.getElementById('chuSuDungContainer');
        const html = `
            <div class="border rounded p-3 bg-light position-relative chu-su-dung-item mb-3" data-index="${chuSuDungIndex}">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2"
                        onclick="removeChuSuDung(this)">X</button>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label>Họ tên <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="chu_su_dung[${chuSuDungIndex}][xung_ho]" class="form-select" style="max-width: 90px;">
                                <option value="Ông">Ông</option>
                                <option value="Bà">Bà</option>
                            </select>
                            <input name="chu_su_dung[${chuSuDungIndex}][ho_ten]" class="form-control" placeholder="Họ và tên" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Ngày sinh</label>
                        <input type="date" name="chu_su_dung[${chuSuDungIndex}][ngay_sinh]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>CCCD/CMND</label>
                        <input name="chu_su_dung[${chuSuDungIndex}][cccd]" class="form-control" placeholder="Số CCCD/CMND">
                    </div>
                    <div class="col-md-4">
                        <label>Ngày cấp</label>
                        <input type="date" name="chu_su_dung[${chuSuDungIndex}][ngay_cap]" class="form-control">
                    </div>
                    <div class="col-12">
                        <label>Địa chỉ</label>
                        <textarea name="chu_su_dung[${chuSuDungIndex}][dia_chi]" class="form-control" rows="2" placeholder="Địa chỉ..."></textarea>
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        chuSuDungIndex++;
    }

    function removeChuSuDung(button) {
        const items = document.querySelectorAll('.chu-su-dung-item');
        if (items.length <= 1) return alert('Phải có ít nhất một chủ sử dụng!');
        button.closest('.chu-su-dung-item').remove();
    }

    function addThua() {
        const container = document.getElementById('thuaContainer');
        const html = `
            <div class="border rounded p-3 mb-3 bg-light position-relative thua-item" data-index="${thuaChungIndex}">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2"
                        onclick="removeThua(this)">X</button>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Tờ</label>
                        <input name="thua_chung[${thuaChungIndex}][to]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Thửa</label>
                        <input name="thua_chung[${thuaChungIndex}][thua]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Diện tích (m²)</label>
                        <input name="thua_chung[${thuaChungIndex}][dien_tich]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Xã</label>
                        <select name="thua_chung[${thuaChungIndex}][xa_id]" class="form-select">
                            @foreach ($xas as $x)
                                <option value="{{ $x->id }}">{{ $x->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label>Ấp / Thôn</label>
                        <input name="thua_chung[${thuaChungIndex}][ap_thon]" class="form-control">
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        thuaChungIndex++;
    }

    function removeThua(button) {
        const items = document.querySelectorAll('.thua-item');
        if (items.length <= 1) return alert('Phải có ít nhất một thửa đất!');
        button.closest('.thua-item').remove();
    }
</script>
