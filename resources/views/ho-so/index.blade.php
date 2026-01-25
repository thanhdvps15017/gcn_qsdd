@extends('welcome')

@section('title', 'Danh sách hồ sơ')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">DANH SÁCH HỒ SƠ</h5>

                    <a href="{{ route('ho-so.create') }}" class="btn btn-light btn-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        Thêm hồ sơ
                    </a>
                </div>

                {{-- FILTER FORM --}}
                <div class="card-body border-bottom">
                    <form method="GET" class="row g-3 align-items-end">

                        {{-- 🔎 Tìm kiếm --}}
                        <div class="col-md-4 col-lg-6">
                            <label class="fw-semibold">Tìm kiếm</label>
                            <input type="text" name="q" class="form-control" placeholder="Mã hồ sơ / Tên chủ hồ sơ"
                                value="{{ request('q') }}">
                        </div>

                        {{-- 📂 Loại hồ sơ --}}
                        <div class="col-md-4 col-lg-3">
                            <label class="fw-semibold">Loại hồ sơ</label>
                            <select name="loai_ho_so_id" class="form-select">
                                <option value="">-- Tất cả --</option>
                                @foreach ($loaiHoSos as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request('loai_ho_so_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 📄 Loại thủ tục --}}
                        <div class="col-md-4 col-lg-3">
                            <label class="fw-semibold">Loại thủ tục</label>
                            <select name="loai_thu_tuc_id" class="form-select">
                                <option value="">-- Tất cả --</option>
                                @foreach ($loaiThuTucs as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request('loai_thu_tuc_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 🏘️ Xã --}}
                        <div class="col-md-4 col-lg-3">
                            <label class="fw-semibold">Người thẩm tra</label>
                            <select name="nguoi_tham_tra_id" class="form-select">
                                <option value="">-- Tất cả --</option>
                                @foreach ($xas as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request('nguoi_tham_tra_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 📌 Trạng thái --}}
                        <div class="col-md-4 col-lg-3">
                            <label class="fw-semibold">Trạng thái</label>
                            <select name="trang_thai" class="form-select">
                                <option value="">-- Tất cả --</option>
                                @php
                                    $statuses = [
                                        'dang_giai_quyet' => 'Đang giải quyết',
                                        'cho_bo_sung' => 'Chờ bổ sung',
                                        'khong_du_dieu_kien' => 'Không đủ điều kiện',
                                        'chuyen_thue' => 'Chuyển thuế',
                                        'hs_niem_yet_xa' => 'Niêm yết xã',
                                        'phoi_hop_do_dac' => 'Phối hợp đo đạc',
                                        'co_phieu_bao' => 'Có phiếu báo',
                                        'in_gcn_qsdd' => 'In GCN QSDĐ',
                                        'hoan_thanh' => 'Hoàn thành',
                                    ];
                                @endphp

                                @foreach ($statuses as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ request('trang_thai') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ⏱️ Sắp xếp --}}
                        <div class="col-md-4 col-lg-3">
                            <label class="fw-semibold">Thời gian</label>
                            <select name="sort" class="form-select">
                                <option value="desc" {{ request('sort', 'desc') === 'desc' ? 'selected' : '' }}>
                                    Mới nhất
                                </option>
                                <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>
                                    Cũ nhất
                                </option>
                            </select>
                        </div>

                        {{-- 🔘 Nút --}}
                        <div class="col-md-4 col-lg-3 d-flex justify-content-end gap-2">
                            <button class="btn btn-primary px-4">
                                <i class="bi bi-search"></i> Lọc
                            </button>

                            <a href="{{ route('ho-so.index') }}" class="btn btn-outline-secondary px-4">
                                Làm mới
                            </a>
                        </div>

                    </form>
                </div>


                {{-- TABLE --}}
                <div class="table-responsive-sm">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="60">#</th>
                                <th>Mã hồ sơ</th>
                                <th>Chủ hồ sơ</th>
                                <th class="d-none d-md-table-cell">Loại hồ sơ</th>
                                <th class="d-none d-md-table-cell">Loại thủ tục</th>
                                <th class="d-none d-md-table-cell">Người thẩm tra</th>
                                <th class="d-none d-md-table-cell">Trạng thái</th>
                                <th width="5%" class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hoSos as $hoSo)
                                @php
                                    $meta = $hoSo->trang_thai_meta;

                                    $rowClass = match ($meta['color'] ?? '') {
                                        'warning' => 'table-warning',
                                        'orange' => 'table-warning',
                                        'danger' => 'table-danger',
                                        'secondary' => 'table-secondary',
                                        default => '',
                                    };

                                    if ($hoSo->trang_thai === 'hoan_thanh') {
                                        $rowClass = 'table-success';
                                    }
                                @endphp

                                <tr class="{{ $rowClass }}">
                                    <td class="text-muted fw-medium">
                                        {{ $loop->iteration + ($hoSos->currentPage() - 1) * $hoSos->perPage() }}
                                    </td>
                                    <td class="fw-medium">{{ $hoSo->ma_ho_so ?? '-' }}</td>

                                    <td>
                                        {{ data_get($hoSo, 'chu_su_dung.ho_ten') ?? '-' }}
                                        @if (!empty($hoSo->sdt_chu_ho_so))
                                            <div class="text-muted small mt-1">
                                                {{ $hoSo->sdt_chu_ho_so }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="d-none d-md-table-cell">{{ optional($hoSo->loaiHoSo)->name ?? '-' }}</td>
                                    <td class="d-none d-md-table-cell">{{ optional($hoSo->loaiThuTuc)->name ?? '-' }}</td>
                                    <td class="d-none d-md-table-cell">{{ optional($hoSo->nguoiThamTra)->name ?? '-' }}</td>

                                    <td class="d-none d-md-table-cell">
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm dropdown-toggle {{ $meta['color'] ? 'badge bg-' . $meta['color'] : 'btn-light' }}"
                                                data-bs-toggle="dropdown">
                                                {{ $meta['text'] ?? '—' }}
                                            </button>

                                            <ul class="dropdown-menu">
                                                @foreach ([
            'dang_giai_quyet' => 'Đang giải quyết',
            'cho_bo_sung' => 'Chờ bổ sung',
            'khong_du_dieu_kien' => 'Không đủ điều kiện',
            'chuyen_thue' => 'Chuyển thuế',
            'hs_niem_yet_xa' => 'Niêm yết xã',
            'phoi_hop_do_dac' => 'Phối hợp đo đạc',
            'co_phieu_bao' => 'Có phiếu báo',
            'in_gcn_qsdd' => 'In GCN QSDĐ',
            'hoan_thanh' => 'Hoàn thành',
        ] as $key => $label)
                                                    <li>
                                                        <a href="#" class="dropdown-item"
                                                            onclick="updateStatus({{ $hoSo->id }}, '{{ $key }}')">
                                                            {{ $label }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </td>

                                    <td class="text-end position-static">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-2" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <a href="{{ route('ho-so.show', $hoSo) }}"
                                                    class="dropdown-item d-flex align-items-center gap-2 text-primary">
                                                    <i class="bi bi-eye"></i>
                                                    <span>Xem chi tiết</span>
                                                </a>

                                                <a href="{{ route('ho-so.edit', $hoSo) }}"
                                                    class="dropdown-item d-flex align-items-center gap-2 text-warning">
                                                    <i class="bi bi-pencil-square"></i>
                                                    <span>Chỉnh sửa</span>
                                                </a>

                                                <form action="{{ route('ho-so.destroy', $hoSo) }}" method="POST"
                                                    onsubmit="return confirm('Bạn chắc chắn muốn xóa hồ sơ {{ $hoSo->ma_ho_so ? '«' . addslashes($hoSo->ma_ho_so) . '»' : '' }} ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                        <i class="bi bi-trash"></i>
                                                        <span>Xoá</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        Chưa có hồ sơ nào được tạo
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER / PAGINATION --}}
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $hoSos->firstItem() ?? 0 }} - {{ $hoSos->lastItem() ?? 0 }}
                        trong {{ $hoSos->total() }} hồ sơ
                    </div>

                    <div>
                        {{ $hoSos->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function updateStatus(id, status) {
            fetch(`/ho-so/${id}/trang-thai`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        trang_thai: status
                    })
                })
                .then(res => res.json())
                .then(() => location.reload());
        }
    </script>
@endsection
