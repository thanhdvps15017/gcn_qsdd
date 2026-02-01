<div class="card mb-3 shadow-sm">
    <div class="card-header fw-bold bg-light d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
        data-bs-target="#collapseThongTinRieng" role="button" style="cursor:pointer;">
        <span>Thông tin sau khi biến động</span>
        <span class="toggle-icon fw-bold fs-5">−</span>
    </div>

    <div id="collapseThongTinRieng" class="collapse">
        <div class="card-body">

            <!-- Loại biến động -->
            <div class="mb-4">
                <label class="form-label fw-bold">Loại biến động</label>
                <select name="thong_tin_rieng[loai]" class="form-select" id="loaiBienDong">
                    <option value="">-- Chọn loại biến động --</option>
                    <option value="tachthua_chuyennhuong"
                        {{ $riengLoai === 'tachthua_chuyennhuong' ? 'selected' : '' }}>Tách thửa - chuyển nhượng
                    </option>
                    <option value="capdoi" {{ $riengLoai === 'capdoi' ? 'selected' : '' }}>Cấp đổi</option>
                    <option value="chuyennhuong" {{ $riengLoai === 'chuyennhuong' ? 'selected' : '' }}>Chuyển nhượng
                    </option>
                    <option value="tachthua" {{ $riengLoai === 'tachthua' ? 'selected' : '' }}>Tách thửa</option>
                    <option value="capdoi_chuyennhuong" {{ $riengLoai === 'capdoi_chuyennhuong' ? 'selected' : '' }}>Cấp
                        đổi + chuyển nhượng</option>
                </select>
            </div>

            <div class="row g-4">

                <!-- Cột trái: Người liên quan -->
                <div class="col-lg-6">
                    <h6 class="fw-bold mb-3 text-success">Người liên quan / Bên nhận chuyển nhượng</h6>

                    <div id="nguoi-lien-quan-container">
                        @forelse ($nguoiLienQuan as $idx => $nguoi)
                            <div class="nguoi-lien-quan-item border rounded p-3 mb-3 bg-light position-relative">
                                <button type="button"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 remove-nguoi-btn">X</button>

                                <div class="row g-3">
                                    <!-- Họ tên + Xưng hô -->
                                    <div class="col-12 col-md-8">
                                        <label class="form-label small">Họ tên <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select
                                                name="thong_tin_rieng[data][nguoi_lien_quan][{{ $idx }}][xung_ho]"
                                                class="form-select" style="max-width: 90px;">
                                                <option value="Ông"
                                                    {{ ($nguoi['xung_ho'] ?? '') == 'Ông' ? 'selected' : '' }}>Ông
                                                </option>
                                                <option value="Bà"
                                                    {{ ($nguoi['xung_ho'] ?? '') == 'Bà' ? 'selected' : '' }}>Bà
                                                </option>
                                            </select>
                                            <input
                                                name="thong_tin_rieng[data][nguoi_lien_quan][{{ $idx }}][ho_ten]"
                                                class="form-control" value="{{ $nguoi['ho_ten'] ?? '' }}"
                                                placeholder="Họ và tên" required>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label small">Ngày sinh</label>
                                        <input type="date"
                                            name="thong_tin_rieng[data][nguoi_lien_quan][{{ $idx }}][ngay_sinh]"
                                            class="form-control" value="{{ $nguoi['ngay_sinh'] ?? '' }}">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">CCCD/CMND</label>
                                        <input name="thong_tin_rieng[data][nguoi_lien_quan][{{ $idx }}][cccd]"
                                            class="form-control" value="{{ $nguoi['cccd'] ?? '' }}"
                                            placeholder="CCCD/CMND">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Ngày cấp</label>
                                        <input type="date"
                                            name="thong_tin_rieng[data][nguoi_lien_quan][{{ $idx }}][ngay_cap_cccd]"
                                            class="form-control" value="{{ $nguoi['ngay_cap_cccd'] ?? '' }}">
                                    </div>

                                    <div class="col-4">
                                        <label class="form-label small">Địa chỉ</label>
                                        <input type="text"
                                            name="thong_tin_rieng[data][nguoi_lien_quan][{{ $idx }}][dia_chi]"
                                            class="form-control" value="{{ $nguoi['dia_chi'] ?? '' }}"
                                            placeholder="Địa chỉ">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="nguoi-lien-quan-item border rounded p-3 mb-3 bg-light position-relative">
                                <button type="button"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 remove-nguoi-btn">X</button>

                                <div class="row g-3">
                                    <div class="col-12 col-md-8">
                                        <label class="form-label small">Họ tên <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="thong_tin_rieng[data][nguoi_lien_quan][0][xung_ho]"
                                                class="form-select" style="max-width: 90px;">
                                                <option value="Ông">Ông</option>
                                                <option value="Bà">Bà</option>
                                            </select>
                                            <input name="thong_tin_rieng[data][nguoi_lien_quan][0][ho_ten]"
                                                class="form-control" placeholder="Họ và tên" required>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label small">Ngày sinh</label>
                                        <input type="date"
                                            name="thong_tin_rieng[data][nguoi_lien_quan][0][ngay_sinh]"
                                            class="form-control">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">CCCD/CMND</label>
                                        <input name="thong_tin_rieng[data][nguoi_lien_quan][0][cccd]"
                                            class="form-control" placeholder="CCCD/CMND">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Ngày cấp</label>
                                        <input type="date"
                                            name="thong_tin_rieng[data][nguoi_lien_quan][0][ngay_cap_cccd]"
                                            class="form-control">
                                    </div>

                                    <div class="col-4">
                                        <label class="form-label small">Địa chỉ</label>
                                        <input type="text"
                                            name="thong_tin_rieng[data][nguoi_lien_quan][0][dia_chi]"
                                            class="form-control" placeholder="Địa chỉ">
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <button type="button" class="btn btn-success btn-sm" onclick="addNguoiLienQuan()">+ Thêm
                        người liên quan</button>
                </div>

                <!-- Cột phải: Danh sách thửa đất -->
                <div class="col-lg-6">
                    <h6 class="fw-bold mb-3 text-success">Danh sách thửa đất sau biến động</h6>

                    <div id="thua-dat-container">
                        @forelse ($riengThua as $idx => $t)
                            <div class="thua-dat-item border rounded p-3 mb-3 bg-light position-relative">
                                <button type="button"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 remove-thua-btn">X</button>

                                <div class="row g-3">
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Tờ</label>
                                        <input name="thong_tin_rieng[data][thua][{{ $idx }}][to]"
                                            class="form-control" value="{{ $t['to'] ?? '' }}" placeholder="Tờ">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Thửa</label>
                                        <input name="thong_tin_rieng[data][thua][{{ $idx }}][thua]"
                                            class="form-control" value="{{ $t['thua'] ?? '' }}" placeholder="Thửa">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Diện tích (m²)</label>
                                        <input name="thong_tin_rieng[data][thua][{{ $idx }}][dien_tich]"
                                            class="form-control" value="{{ $t['dien_tich'] ?? '' }}"
                                            placeholder="Diện tích">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small">Ghi chú</label>
                                        <input name="thong_tin_rieng[data][thua][{{ $idx }}][ghi_chu]"
                                            class="form-control" value="{{ $t['ghi_chu'] ?? '' }}"
                                            placeholder="Ghi chú (nếu có)">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="thua-dat-item border rounded p-3 mb-3 bg-light position-relative">
                                <button type="button"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 remove-thua-btn">X</button>

                                <div class="row g-3">
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Tờ</label>
                                        <input name="thong_tin_rieng[data][thua][0][to]" class="form-control"
                                            placeholder="Tờ">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Thửa</label>
                                        <input name="thong_tin_rieng[data][thua][0][thua]" class="form-control"
                                            placeholder="Thửa">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Diện tích (m²)</label>
                                        <input name="thong_tin_rieng[data][thua][0][dien_tich]" class="form-control"
                                            placeholder="Diện tích">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small">Ghi chú</label>
                                        <input name="thong_tin_rieng[data][thua][0][ghi_chu]" class="form-control"
                                            placeholder="Ghi chú (nếu có)">
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <button type="button" class="btn btn-success btn-sm" onclick="addThuaDat()">+ Thêm
                        thửa</button>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    let nguoiLienQuanIndex = {{ $nguoiIndex ?? count($nguoiLienQuan ?? []) }};
    let thuaDatIndex = {{ count($riengThua ?? []) }};

    function addNguoiLienQuan() {
        const container = document.getElementById('nguoi-lien-quan-container');
        if (!container) return;

        const html = `
            <div class="nguoi-lien-quan-item border rounded p-3 mb-3 bg-light position-relative">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 remove-nguoi-btn">X</button>

                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label small">Họ tên <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="thong_tin_rieng[data][nguoi_lien_quan][${nguoiLienQuanIndex}][xung_ho]" 
                                    class="form-select" style="max-width: 90px;">
                                <option value="Ông">Ông</option>
                                <option value="Bà">Bà</option>
                            </select>
                            <input name="thong_tin_rieng[data][nguoi_lien_quan][${nguoiLienQuanIndex}][ho_ten]" 
                                   class="form-control" placeholder="Họ và tên" required>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small">Ngày sinh</label>
                        <input type="date" name="thong_tin_rieng[data][nguoi_lien_quan][${nguoiLienQuanIndex}][ngay_sinh]" class="form-control">
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label small">CCCD/CMND</label>
                        <input name="thong_tin_rieng[data][nguoi_lien_quan][${nguoiLienQuanIndex}][cccd]" class="form-control" placeholder="CCCD/CMND">
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label small">Ngày cấp</label>
                        <input type="date" name="thong_tin_rieng[data][nguoi_lien_quan][${nguoiLienQuanIndex}][ngay_cap_cccd]" class="form-control">
                    </div>

                    <div class="col-4">
                        <label class="form-label small">Địa chỉ</label>
                        <input type="text" name="thong_tin_rieng[data][nguoi_lien_quan][${nguoiLienQuanIndex}][dia_chi]" 
                               class="form-control" placeholder="Địa chỉ">
                    </div>
                </div>
            </div>`;

        container.insertAdjacentHTML('beforeend', html);
        nguoiLienQuanIndex++;
    }

    function addThuaDat() {
        const container = document.getElementById('thua-dat-container');
        if (!container) return;

        const html = `
            <div class="thua-dat-item border rounded p-3 mb-3 bg-light position-relative">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 remove-thua-btn">X</button>

                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <label class="form-label small">Tờ</label>
                        <input name="thong_tin_rieng[data][thua][${thuaDatIndex}][to]" class="form-control" placeholder="Tờ">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label small">Thửa</label>
                        <input name="thong_tin_rieng[data][thua][${thuaDatIndex}][thua]" class="form-control" placeholder="Thửa">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label small">Diện tích (m²)</label>
                        <input name="thong_tin_rieng[data][thua][${thuaDatIndex}][dien_tich]" class="form-control" placeholder="Diện tích">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Ghi chú</label>
                        <input name="thong_tin_rieng[data][thua][${thuaDatIndex}][ghi_chu]" class="form-control" placeholder="Ghi chú (nếu có)">
                    </div>
                </div>
            </div>`;

        container.insertAdjacentHTML('beforeend', html);
        thuaDatIndex++;
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-nguoi-btn')) {
            const items = document.querySelectorAll('.nguoi-lien-quan-item');
            if (items.length <= 1) {
                alert('Phải có ít nhất một người liên quan!');
                return;
            }
            e.target.closest('.nguoi-lien-quan-item').remove();
        }

        if (e.target.classList.contains('remove-thua-btn')) {
            const items = document.querySelectorAll('.thua-dat-item');
            if (items.length <= 1) {
                alert('Phải có ít nhất một thửa đất!');
                return;
            }
            e.target.closest('.thua-dat-item').remove();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const header = document.querySelector('[data-bs-target="#collapseThongTinRieng"]');
        if (header) {
            header.addEventListener('click', () => {
                const icon = header.querySelector('.toggle-icon');
                const collapse = document.getElementById('collapseThongTinRieng');
                icon.textContent = collapse.classList.contains('show') ? '−' : '+';
            });
        }
    });
</script>
