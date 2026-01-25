@extends('welcome')

@section('content')
    <!-- Header -->
    <div class="card-header text-white d-flex justify-content-between align-items-center rounded-1"
        style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
        <h5 class="mb-0 fw-bold">
            Sổ: {{ $group->ma_so }} - {{ $group->ten_so }}
        </h5>

        <a href="{{ route('so-theo-doi.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row g-4 pt-3">
        <!-- CỘT TRÁI -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-1 h-100">
                <div class="card-header bg-success text-white fw-bold">
                    Thêm hồ sơ vào sổ
                </div>

                <div class="card-body p-2">
                    <form method="POST" action="{{ route('so-theo-doi.batch-add', $group) }}">
                        @csrf

                        <input type="text" id="search-chua-them" class="form-control mb-2"
                            placeholder="🔍 Tìm hồ sơ chưa thêm...">

                        <select name="ho_so_ids[]" id="list-chua-them" class="form-select" multiple required
                            style="min-height:350px">
                            @foreach ($hoSosChuaThem as $hs)
                                <option value="{{ $hs->id }}">
                                    {{ $hs->ma_ho_so }} - {{ $hs->ten_chu_ho_so ?? 'Không tên' }}
                                </option>
                            @endforeach
                        </select>

                        <button class="btn btn-success w-100 mt-2">
                            <i class="bi bi-plus-lg"></i> Thêm hồ sơ
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-1 h-100">
                <div class="card-header bg-info text-white fw-bold">
                    Hồ sơ trong sổ
                </div>

                <div class="card-body p-2">
                    <input type="text" id="search-trong-so" class="form-control mb-2"
                        placeholder="🔍 Tìm hồ sơ trong sổ...">

                    <form method="POST" action="{{ route('so-theo-doi.batch-remove', $group) }}" id="batch-remove-form">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="check-all">
                                        </th>
                                        <th>Mã HS</th>
                                        <th>Chủ hồ sơ</th>
                                        <th>Trạng thái</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody id="table-trong-so">
                                    @foreach ($hoSosTrongSo as $hs)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ho_so_ids[]" value="{{ $hs->id }}">
                                            </td>
                                            <td>{{ $hs->ma_ho_so }}</td>
                                            <td>{{ $hs->ten_chu_ho_so }}</td>
                                            @php
                                                $map = [
                                                    'dang_giai_quyet' => 'Đang giải quyết',
                                                    'cho_bo_sung' => 'Chờ bổ sung',
                                                    'khong_du_dieu_kien' => 'Không đủ điều kiện',
                                                    'chuyen_thue' => 'Chuyển thuế',
                                                    'hs_niem_yet_xa' => 'Niêm yết xã',
                                                    'phoi_hop_do_dac' => 'Phối hợp đo đạc',
                                                    'co_phieu_bao' => 'Có phiếu báo',
                                                    'in_gcn_qsdd' => 'In GCN QSDĐ',
                                                ];
                                            @endphp

                                            <td>{{ $map[$hs->trang_thai] ?? $hs->trang_thai }}</td>
                                            <td>
                                                <a href="{{ route('ho-so.show', $hs) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    Xem
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button class="btn btn-danger btn-sm mt-2" onclick="return confirm('Xóa hồ sơ đã chọn?')">
                            <i class="bi bi-trash"></i> Xóa chọn
                        </button>
                    </form>

                    <div class="mt-2">
                        {{ $hoSosTrongSo->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= JS ================= --}}
    <script>
        /* CHECK ALL */
        document.getElementById('check-all').addEventListener('click', e => {
            document.querySelectorAll('input[name="ho_so_ids[]"]').forEach(cb => cb.checked = e.target.checked);
        });

        /* SEARCH CHƯA THÊM */
        document.getElementById('search-chua-them').addEventListener('input', function() {
            fetch(`{{ route('so-theo-doi.search-chua-them', $group) }}?q=${this.value}`)
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('list-chua-them');
                    select.innerHTML = '';
                    data.forEach(hs => {
                        select.innerHTML += `
                    <option value="${hs.id}">
                        ${hs.ma_ho_so} - ${hs.ten_chu_ho_so ?? 'Không tên'}
                    </option>`;
                    });
                });
        });

        /* SEARCH TRONG SỔ */
        document.getElementById('search-trong-so').addEventListener('input', function() {
            fetch(`{{ route('so-theo-doi.search-trong-so', $group) }}?q=${this.value}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('table-trong-so');
                    tbody.innerHTML = '';

                    if (!data.length) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">
                    Không tìm thấy hồ sơ
                </td></tr>`;
                        return;
                    }

                    data.forEach(hs => {
                        tbody.innerHTML += `
                <tr>
                    <td><input type="checkbox" name="ho_so_ids[]" value="${hs.id}"></td>
                    <td>${hs.ma_ho_so}</td>
                    <td>${hs.chu_su_dung?.ho_ten ?? '-'}</td>
                    <td>${hs.trang_thai ?? ''}</td>
                    <td>
                        <a href="/ho-so/${hs.id}" class="btn btn-sm btn-outline-primary">Xem</a>
                    </td>
                </tr>`;
                    });
                });
        });
    </script>
@endsection
