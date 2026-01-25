@extends('welcome')

@section('content')
    <h2 class="mb-4" style="color: var(--primary);">Thêm mới hồ sơ</h2> <!-- Thông tin chung -->
    <div class="card mb-4">
        <div class="card-header">Thông tin chung</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"> <label class="form-label">Mã hồ sơ</label>
                    <div class="input-group"> <span class="input-group-text prefix-code">H19.151-</span> <input type="text"
                            class="form-control" placeholder="Nhập phần còn lại"> </div>
                </div>
                <div class="col-md-6"> <label class="form-label">Loại hồ sơ</label> <select class="form-select">
                        <option>Chọn loại hồ sơ...</option>
                        <option>GCN quyền sử dụng đất</option>
                        <option>Đo đạc</option>
                    </select> </div>
                <div class="col-md-6"> <label class="form-label">Loại thủ tục</label> <select class="form-select">
                        <option>Chọn loại thủ tục...</option>
                        <option>Tách thửa</option>
                        <option>Chuyển nhượng</option>
                        <option>Cấp đổi</option>
                    </select> </div>
                <div class="col-md-6"> <label class="form-label">Hành chính công (Xã)</label> <select class="form-select">
                        <option>Lộc Hưng</option>
                        <option>Lộc Ninh</option>
                        <option>Lộc Thạnh</option>
                        <option>Lộc Thành</option>
                        <option>Lộc Quang</option>
                        <option>Lộc Tấn</option>
                    </select> </div>
                <div class="col-md-6"> <label class="form-label">Người thẩm tra</label> <select class="form-select">
                        <option>Chọn người...</option>
                        <option>Nguyễn Văn A</option>
                        <option>Trần Thị B</option>
                    </select> </div>
                <div class="col-md-6"> <label class="form-label">Chủ hồ sơ - SĐT</label> <input type="tel"
                        class="form-control" placeholder="090xxxxxxx"> </div>
            </div>
        </div>
    </div> <!-- Thông tin chủ sử dụng -->
    <div class="card mb-4">
        <div class="card-header">Thông tin chủ sử dụng</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"> <label class="form-label">Họ tên (Ông / Bà)</label> <input type="text"
                        class="form-control"> </div>
                <div class="col-md-6"> <label class="form-label">CMND / CCCD</label> <input type="text"
                        class="form-control"> </div>
                <div class="col-md-6"> <label class="form-label">Ngày cấp</label> <input type="date"
                        class="form-control"> </div>
                <div class="col-12"> <label class="form-label">Địa chỉ</label>
                    <textarea class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="mt-4"> <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse"
                    data-bs-target="#uyquyen" aria-expanded="false"> Có người ủy quyền / Giấy ủy quyền </button> </div>
            <div class="collapse mt-4" id="uyquyen">
                <div class="mb-3"> <label class="form-label">Người ủy quyền</label>
                    <textarea class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3"> <label class="form-label">Giấy ủy quyền</label>
                    <textarea class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div> <!-- Thông tin thửa -->
    <div class="card mb-4">
        <div class="card-header">Thông tin thửa</div>
        <div class="card-body"> <label class="form-label fw-bold">Thửa - tờ - diện tích</label>
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="tblThua">
                    <thead class="table-light">
                        <tr>
                            <th>Thửa số</th>
                            <th>Tờ bản đồ</th>
                            <th>Diện tích (m²)</th>
                            <th style="width:80px"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div> <button type="button" class="btn btn-success btn-sm mt-2" onclick="addRow('tblThua')"> <i
                    class="bi bi-plus"></i> Thêm dòng </button>
            <div class="row g-3 mt-4">
                <div class="col-md-4"> <label class="form-label">Ngày cấp GCN</label> <input type="date"
                        class="form-control"> </div>
                <div class="col-md-4"> <label class="form-label">Số vào sổ GCN</label> <input type="text"
                        class="form-control"> </div>
                <div class="col-md-4"> <label class="form-label">Số phát hành</label> <input type="text"
                        class="form-control"> </div>
            </div>
        </div>
    </div> <!-- Thông tin riêng -->
    <div class="card mb-4">
        <div class="card-header">Thông tin riêng</div>
        <div class="card-body">
            <div class="mb-4"> <label class="form-label fw-bold">Chọn loại thủ tục chi tiết</label> <select
                    class="form-select" id="loaiRieng" onchange="showForm()">
                    <option value="">-- Chọn để hiển thị --</option>
                    <option value="tachthua_chuyennhuong">Tách thửa - chuyển nhượng</option>
                    <option value="capdoi_chuyennhuong">Cấp đổi + chuyển nhượng</option>
                    <option value="chuyennhuong">Chuyển nhượng</option>
                    <option value="tachthua">Tách thửa</option>
                    <option value="capdoi">Cấp đổi</option>
                </select> </div>
            <div id="form_tachthua_chuyennhuong" class="sub-form d-none">
                <h5 class="mb-3">Tách thửa - chuyển nhượng</h5>
                <div class="row g-3">
                    <div class="col-md-6"> <label class="form-label">Họ tên</label> <input type="text"
                            class="form-control"> </div>
                    <div class="col-12"> <label class="form-label">Địa chỉ</label>
                        <textarea class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="mt-4"> <label class="form-label fw-bold">Tờ - thửa - diện tích</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="tblTachChuyen">
                            <thead class="table-light">
                                <tr>
                                    <th>Tờ</th>
                                    <th>Thửa</th>
                                    <th>Diện tích (m²)</th>
                                    <th style="width:80px"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div> <button type="button" class="btn btn-success btn-sm mt-2" onclick="addRow('tblTachChuyen')">
                        <i class="bi bi-plus"></i> Thêm dòng </button>
                </div>
            </div>
        </div>
    </div> <!-- Ghi chú -->
    <div class="card mb-4">
        <div class="card-header">Ghi chú hệ thống</div>
        <div class="card-body">
            <textarea class="form-control" rows="6" placeholder="Ghi chú..."></textarea>
        </div>
    </div>
    <div class="text-end mt-4"> <button class="btn btn-secondary me-2">Hủy</button> <button
            class="btn btn-success me-2">Lưu hồ sơ</button> <button class="btn btn-primary">Xuất báo cáo</button> </div>
    <script>
        function addRow(tableId) {
            const tbody = document.getElementById(tableId).tBodies[0];
            const row = tbody.insertRow();
            let html = '';
            if (tableId === 'tblCapdoi') {
                html = < td > < input type = "text"
                class = "form-control form-control-sm" > <
                /td> <td><input type="text" class="form-control form-control-sm"></td > < td > < input type = "number"
                class = "form-control form-control-sm"
                step = "0.01" > < /td> <td><input type="number" class="form-control form-control-sm" step="0.01"></td > <
                    td > < input type = "text"
                class = "form-control form-control-sm" > <
                /td> <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Xóa</button >
                <
                /td> ; } else { html = <td><input type="text" class="form-control form-control-sm"></td > < td > <
                    input type = "text"
                class = "form-control form-control-sm" > <
                /td> <td><input type="number" class="form-control form-control-sm" step="0.01" min="0"></td > < td > <
                    button type = "button"
                class = "btn btn-danger btn-sm"
                onclick = "this.closest('tr').remove()" > Xóa < /button></td > ;
            }
            row.innerHTML = html;
        }

        function showForm() {
            document.querySelectorAll('.sub-form').forEach(el => el.classList.add('d-none'));
            const val = document.getElementById('loaiRieng').value;
            if (val) {
                const target = document.getElementById(form_$ {
                    val
                });
                if (target) target.classList.remove('d-none');
            }
        }
        addRow('tblThua');
    </script>
@endsection
