<div class="row mb-3">

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
                                <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="land_owners[{{ $idx }}][salutation]" class="form-select"
                                        style="max-width: 90px;">
                                        <option value="Ông" {{ ($chu['salutation'] ?? '') == 'Ông' ? 'selected' : '' }}>
                                            Ông</option>
                                        <option value="Bà" {{ ($chu['salutation'] ?? '') == 'Bà' ? 'selected' : '' }}>
                                            Bà</option>
                                    </select>

                                    <input name="land_owners[{{ $idx }}][full_name]" class="form-control"
                                        value="{{ old("land_owners.$idx.full_name", $chu['full_name'] ?? '') }}"
                                        placeholder="Nhập họ và tên đầy đủ" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" name="land_owners[{{ $idx }}][date_of_birth]"
                                    class="form-control"
                                    value="{{ old("land_owners.$idx.date_of_birth", $chu['date_of_birth'] ?? '') }}"
                                    placeholder="YYYY-MM-DD">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">CCCD/CMND</label>
                                <input name="land_owners[{{ $idx }}][id_card]" class="form-control"
                                    value="{{ old("land_owners.$idx.id_card", $chu['id_card'] ?? '') }}"
                                    placeholder="Nhập số CCCD/CMND">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Ngày cấp</label>
                                <input type="date" name="land_owners[{{ $idx }}][issue_date]"
                                    class="form-control"
                                    value="{{ old("land_owners.$idx.issue_date", $chu['issue_date'] ?? '') }}"
                                    placeholder="YYYY-MM-DD">
                            </div>

                            <div class="col-4">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" name="land_owners[{{ $idx }}][address]"
                                    class="form-control"
                                    value="{{ old("land_owners.$idx.address", $chu['address'] ?? '') }}"
                                    placeholder="Số nhà, đường, xã/phường...">
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer bg-light border-0">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Người uỷ quyền</label>
                        <input type="text" name="authorization[person]" class="form-control"
                            placeholder="Nhập tên người ủy quyền"
                            value="{{ old('authorization.person', $uyQuyen['person'] ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Giấy uỷ quyền</label>
                        <input type="text" name="authorization[paper]" class="form-control"
                            placeholder="Số giấy ủy quyền / ngày ký"
                            value="{{ old('authorization.paper', $uyQuyen['paper'] ?? '') }}">
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
                                <label class="form-label">Tờ</label>
                                <input name="shared_plots[{{ $idx }}][to]" class="form-control"
                                    value="{{ $row['map_sheet'] ?? '' }}" placeholder="Số tờ bản đồ">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Thửa</label>
                                <input name="shared_plots[{{ $idx }}][thua]" class="form-control"
                                    value="{{ $row['plot_number'] ?? '' }}" placeholder="Số thửa">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Diện tích (m²)</label>
                                <input name="shared_plots[{{ $idx }}][area]" class="form-control"
                                    value="{{ $row['area'] ?? '' }}" placeholder="VD: 120.5">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Xã</label>
                                <select name="shared_plots[{{ $idx }}][ward_id]" class="form-select">
                                    @foreach ($xas as $x)
                                        <option value="{{ $x->id }}"
                                            {{ ($row['ward_id'] ?? '') == $x->id ? 'selected' : '' }}>
                                            {{ $x->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Ấp / Thôn</label>
                                <input name="shared_plots[{{ $idx }}][hamlet]" class="form-control"
                                    value="{{ $row['hamlet'] ?? '' }}" placeholder="Tên ấp / thôn">
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer bg-light border-0">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Ngày cấp GCN</label>
                        <input type="date" name="certificate_issue_date" class="form-control" placeholder="YYYY-MM-DD"
                            value="{{ old('certificate_issue_date', $isEdit ? optional($hoSo->certificate_issue_date)->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Số vào sổ</label>
                        <input name="registration_book_number" class="form-control" placeholder="Nhập số vào sổ"
                            value="{{ $getValue('registration_book_number') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Số phát hành</label>
                        <input name="publication_number" class="form-control" placeholder="Nhập số phát hành"
                            value="{{ $getValue('publication_number') }}">
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>


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
                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select name="land_owners[${chuSuDungIndex}][salutation]" class="form-select" style="max-width: 90px;">
                            <option value="Ông">Ông</option>
                            <option value="Bà">Bà</option>
                        </select>
                        <input name="land_owners[${chuSuDungIndex}][full_name]" class="form-control" placeholder="Nhập họ và tên đầy đủ" required>
                    </div>
                </div>
    
                <div class="col-md-4">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" name="land_owners[${chuSuDungIndex}][date_of_birth]" class="form-control" placeholder="YYYY-MM-DD">
                </div>
    
                <div class="col-md-4">
                    <label class="form-label">CCCD/CMND</label>
                    <input name="land_owners[${chuSuDungIndex}][id_card]" class="form-control" placeholder="Nhập số CCCD/CMND">
                </div>
    
                <div class="col-md-4">
                    <label class="form-label">Ngày cấp</label>
                    <input type="date" name="land_owners[${chuSuDungIndex}][issue_date]" class="form-control" placeholder="YYYY-MM-DD">
                </div>
    
                <div class="col-4">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="land_owners[${chuSuDungIndex}][address]" class="form-control" placeholder="Số nhà, đường, xã/phường...">
                </div>
    
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
        chuSuDungIndex++;
    }

    function removeChuSuDung(btn) {
        const items = document.querySelectorAll('.chu-su-dung-item');
        if (items.length <= 1) return alert('Phải có ít nhất một chủ sử dụng!');
        btn.closest('.chu-su-dung-item').remove();
    }

    function addThua() {
        const container = document.getElementById('thuaContainer');

        const html = `
        <div class="border rounded p-3 mb-3 bg-light position-relative thua-item" data-index="${thuaChungIndex}">
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2"
                onclick="removeThua(this)">X</button>
    
            <div class="row g-3">
    
                <div class="col-md-4">
                    <label class="form-label">Tờ</label>
                    <input name="shared_plots[${thuaChungIndex}][to]" class="form-control" placeholder="Số tờ bản đồ">
                </div>
    
                <div class="col-md-4">
                    <label class="form-label">Thửa</label>
                    <input name="shared_plots[${thuaChungIndex}][thua]" class="form-control" placeholder="Số thửa">
                </div>
    
                <div class="col-md-4">
                    <label class="form-label">Diện tích (m²)</label>
                    <input name="shared_plots[${thuaChungIndex}][area]" class="form-control" placeholder="VD: 120.5">
                </div>
    
                <div class="col-md-4">
                    <label class="form-label">Xã</label>
                    <select name="shared_plots[${thuaChungIndex}][ward_id]" class="form-select">
                        @foreach ($xas as $x)
                            <option value="{{ $x->id }}">{{ $x->name }}</option>
                        @endforeach
                    </select>
                </div>
    
                <div class="col-md-8">
                    <label class="form-label">Ấp / Thôn</label>
                    <input name="shared_plots[${thuaChungIndex}][hamlet]" class="form-control" placeholder="Tên ấp / thôn">
                </div>
    
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
        thuaChungIndex++;
    }

    function removeThua(btn) {
        const items = document.querySelectorAll('.thua-item');
        if (items.length <= 1) return alert('Phải có ít nhất một thửa đất!');
        btn.closest('.thua-item').remove();
    }
</script>
