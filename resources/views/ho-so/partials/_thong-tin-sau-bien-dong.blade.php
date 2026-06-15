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
                <select name="private_info[type]" class="form-select" id="loaiBienDong">
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

                    <div id="person-lien-quan-container">
                        @forelse ($nguoiLienQuan as $idx => $person)
                            <div class="person-lien-quan-item border rounded p-3 mb-3 bg-light position-relative">
                                <button type="button"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 remove-person-btn">X</button>

                                <div class="row g-3">
                                    <!-- Họ tên + Xưng hô -->
                                    <div class="col-12 col-md-8">
                                        <label class="form-label small">Họ tên <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select
                                                name="private_info[data][related_person][{{ $idx }}][salutation]"
                                                class="form-select" style="max-width: 90px;">
                                                <option value="Ông"
                                                    {{ ($person['salutation'] ?? '') == 'Ông' ? 'selected' : '' }}>Ông
                                                </option>
                                                <option value="Bà"
                                                    {{ ($person['salutation'] ?? '') == 'Bà' ? 'selected' : '' }}>Bà
                                                </option>
                                            </select>
                                            <input
                                                name="private_info[data][related_person][{{ $idx }}][full_name]"
                                                class="form-control" value="{{ $person['full_name'] ?? '' }}"
                                                placeholder="Họ và tên" required>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label small">Ngày sinh</label>
                                        <input type="date"
                                            name="private_info[data][related_person][{{ $idx }}][date_of_birth]"
                                            class="form-control" value="{{ $person['date_of_birth'] ?? '' }}">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">CCCD/CMND</label>
                                        <input name="private_info[data][related_person][{{ $idx }}][id_card]"
                                            class="form-control" value="{{ $person['id_card'] ?? '' }}"
                                            placeholder="CCCD/CMND">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Ngày cấp</label>
                                        <input type="date"
                                            name="private_info[data][related_person][{{ $idx }}][id_issue_date]"
                                            class="form-control" value="{{ $person['id_issue_date'] ?? '' }}">
                                    </div>

                                    <div class="col-4">
                                        <label class="form-label small">Địa chỉ</label>
                                        <input type="text"
                                            name="private_info[data][related_person][{{ $idx }}][address]"
                                            class="form-control" value="{{ $person['address'] ?? '' }}"
                                            placeholder="Địa chỉ">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="person-lien-quan-item border rounded p-3 mb-3 bg-light position-relative">
                                <button type="button"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 remove-person-btn">X</button>

                                <div class="row g-3">
                                    <div class="col-12 col-md-8">
                                        <label class="form-label small">Họ tên <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select name="private_info[data][related_person][0][salutation]"
                                                class="form-select" style="max-width: 90px;">
                                                <option value="Ông">Ông</option>
                                                <option value="Bà">Bà</option>
                                            </select>
                                            <input name="private_info[data][related_person][0][full_name]"
                                                class="form-control" placeholder="Họ và tên" required>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label small">Ngày sinh</label>
                                        <input type="date"
                                            name="private_info[data][related_person][0][date_of_birth]"
                                            class="form-control">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">CCCD/CMND</label>
                                        <input name="private_info[data][related_person][0][id_card]"
                                            class="form-control" placeholder="CCCD/CMND">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Ngày cấp</label>
                                        <input type="date"
                                            name="private_info[data][related_person][0][id_issue_date]"
                                            class="form-control">
                                    </div>

                                    <div class="col-4">
                                        <label class="form-label small">Địa chỉ</label>
                                        <input type="text"
                                            name="private_info[data][related_person][0][address]"
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
                                        <input name="private_info[data][plot_number][{{ $idx }}][map_sheet]"
                                            class="form-control" value="{{ $t['map_sheet'] ?? $t['to'] ?? '' }}" placeholder="Tờ">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Thửa</label>
                                        <input name="private_info[data][plot_number][{{ $idx }}][plot_number]"
                                            class="form-control" value="{{ $t['plot_number'] ?? $t['thua'] ?? '' }}" placeholder="Thửa">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Diện tích (m²)</label>
                                        <input name="private_info[data][plot_number][{{ $idx }}][area]"
                                            class="form-control" value="{{ $t['area'] ?? $t['dien_tich'] ?? '' }}"
                                            placeholder="Diện tích">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small">Ghi chú</label>
                                        <input name="private_info[data][plot_number][{{ $idx }}][notes]"
                                            class="form-control" value="{{ $t['notes'] ?? '' }}"
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
                                        <input name="private_info[data][plot_number][0][map_sheet]" class="form-control"
                                            placeholder="Tờ">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Thửa</label>
                                        <input name="private_info[data][plot_number][0][plot_number]" class="form-control"
                                            placeholder="Thửa">
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label small">Diện tích (m²)</label>
                                        <input name="private_info[data][plot_number][0][area]" class="form-control"
                                            placeholder="Diện tích">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small">Ghi chú</label>
                                        <input name="private_info[data][plot_number][0][notes]" class="form-control"
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
        const container = document.getElementById('person-lien-quan-container');
        if (!container) return;

        const html = `
            <div class="person-lien-quan-item border rounded p-3 mb-3 bg-light position-relative">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 remove-person-btn">X</button>

                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label small">Họ tên <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="private_info[data][related_person][${nguoiLienQuanIndex}][salutation]" 
                                    class="form-select" style="max-width: 90px;">
                                <option value="Ông">Ông</option>
                                <option value="Bà">Bà</option>
                            </select>
                            <input name="private_info[data][related_person][${nguoiLienQuanIndex}][full_name]" 
                                   class="form-control" placeholder="Họ và tên" required>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small">Ngày sinh</label>
                        <input type="date" name="private_info[data][related_person][${nguoiLienQuanIndex}][date_of_birth]" class="form-control">
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label small">CCCD/CMND</label>
                        <input name="private_info[data][related_person][${nguoiLienQuanIndex}][id_card]" class="form-control" placeholder="CCCD/CMND">
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label small">Ngày cấp</label>
                        <input type="date" name="private_info[data][related_person][${nguoiLienQuanIndex}][id_issue_date]" class="form-control">
                    </div>

                    <div class="col-4">
                        <label class="form-label small">Địa chỉ</label>
                        <input type="text" name="private_info[data][related_person][${nguoiLienQuanIndex}][address]" 
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
                        <input name="private_info[data][plot_number][${thuaDatIndex}][map_sheet]" class="form-control" placeholder="Tờ">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label small">Thửa</label>
                        <input name="private_info[data][plot_number][${thuaDatIndex}][plot_number]" class="form-control" placeholder="Thửa">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label small">Diện tích (m²)</label>
                        <input name="private_info[data][plot_number][${thuaDatIndex}][area]" class="form-control" placeholder="Diện tích">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Ghi chú</label>
                        <input name="private_info[data][plot_number][${thuaDatIndex}][notes]" class="form-control" placeholder="Ghi chú (nếu có)">
                    </div>
                </div>
            </div>`;

        container.insertAdjacentHTML('beforeend', html);
        thuaDatIndex++;
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-person-btn')) {
            const items = document.querySelectorAll('.person-lien-quan-item');
            if (items.length <= 1) {
                alert('Phải có ít nhất một người liên quan!');
                return;
            }
            e.target.closest('.person-lien-quan-item').remove();
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
