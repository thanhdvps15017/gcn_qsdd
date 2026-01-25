@extends('welcome')

@section('content')
    <!-- Header -->
    <div class="card-header text-white d-flex justify-content-between align-items-center mb-4"
        style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-file-earmark-text-fill me-3 fs-3"></i>
            Hồ sơ: {{ $hoSo->ma_ho_so }}
        </h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('ho-so.index') }}" class="btn btn-outline-light btn-md px-3 py-2">
                <i class="bi bi-arrow-left me-2"></i> Quay lại
            </a>
            <a href="{{ route('ho-so.edit', $hoSo) }}" class="btn btn-outline-light btn-md px-3 py-2">
                <i class="bi bi-pencil-square me-2"></i> Sửa
            </a>
            <form action="{{ route('ho-so.destroy', $hoSo) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Bạn có chắc chắn muốn xóa hồ sơ này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-light btn-md px-3 py-2">
                    <i class="bi bi-trash3 me-2"></i> Xóa
                </button>
            </form>
        </div>
    </div>

    <!-- Trạng thái nổi bật -->
    <div class="alert alert-light border shadow-sm mb-3 px-4 py-2 d-flex align-items-center gap-2">
        <div class="fw-bold fs-5 text-dark">Trạng thái hiện tại:</div>
        @php
            $status = $hoSo->trang_thai ?? 'dang_giai_quyet';
            $badgeClass = match ($status) {
                'dang_giai_quyet' => 'bg-warning text-dark',
                'da_giai_quyet' => 'bg-success text-white',
                'tu_choi' => 'bg-danger text-white',
                default => 'bg-secondary text-white',
            };
        @endphp
        {{ str_replace('_', ' ', ucwords($status)) }}
    </div>

    <!-- Thông tin chung -->
    <div class="card shadow border-0 mb-3 rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white fw-bold fs-5 d-flex align-items-center px-4 py-2">
            <i class="bi bi-info-circle-fill me-3 fs-4"></i> Thông tin chung
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Mã hồ sơ</label>
                    <p class="fs-5 mb-0 fw-medium">{{ $hoSo->ma_ho_so ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Xưng hô & Tên chủ hồ sơ</label>
                    <p class="fs-5 mb-0 fw-medium">
                        {{ ucfirst($hoSo->xung_ho ?? '') }} {{ $hoSo->ten_chu_ho_so ?? '-' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">SĐT chủ hồ sơ</label>
                    <p class="fs-5 mb-0 fw-medium">{{ $hoSo->sdt_chu_ho_so ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Hạn giải quyết</label>
                    <p class="fs-5 mb-0 fw-medium">
                        {{ $hoSo->han_giai_quyet ? \Carbon\Carbon::parse($hoSo->han_giai_quyet)->format('d/m/Y') : '-' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Loại hồ sơ</label>
                    <p class="fs-5 mb-0 fw-medium">{{ optional($hoSo->loaiHoSo)->name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Loại thủ tục</label>
                    <p class="fs-5 mb-0 fw-medium">{{ optional($hoSo->loaiThuTuc)->name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Xã / Phường</label>
                    <p class="fs-5 mb-0 fw-medium">{{ optional($hoSo->xa)->name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Người thẩm tra</label>
                    <p class="fs-5 mb-0 fw-medium">{{ optional($hoSo->nguoiThamTra)->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chủ sử dụng & Ủy quyền -->
    <div class="card shadow border-0 mb-3 rounded-4 overflow-hidden">
        <div class="card-header bg-info text-white fw-bold fs-5 d-flex align-items-center px-4 py-2">
            <i class="bi bi-person-badge-fill me-3 fs-4"></i> Chủ sử dụng & Ủy quyền
        </div>
        <div class="card-body p-4">
            @php $chu = $hoSo->chu_su_dung ?? []; @endphp
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small">Họ tên</label>
                    <p class="fs-5 mb-0 fw-medium">{{ $chu['ho_ten'] ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small">CCCD/CMND</label>
                    <p class="fs-5 mb-0 fw-medium">{{ $chu['cccd'] ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small">Ngày cấp</label>
                    <p class="fs-5 mb-0 fw-medium">{{ $chu['ngay_cap'] ?? '-' }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-muted small">Địa chỉ</label>
                    <p class="fs-5 mb-0 fw-medium">{{ $chu['dia_chi'] ?? '-' }}</p>
                </div>
            </div>

            @php $uy = $hoSo->uy_quyen ?? []; @endphp
            @if (!empty($uy['nguoi']) || !empty($uy['giay']))
                <hr class="my-5">
                <h5 class="fw-bold text-info mb-4">Thông tin ủy quyền</h5>
                <div class="row g-4">
                    <div class="col-6">
                        <label class="form-label fw-semibold text-muted small">Người ủy quyền</label>
                        <p class="fs-5 mb-0 fw-medium">{{ $uy['nguoi'] ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold text-muted small">Giấy ủy quyền</label>
                        <p class="fs-5 mb-0 fw-medium">{{ $uy['giay'] ?? '-' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Thửa chung -->
    <div class="card shadow border-0 mb-3 rounded-4 overflow-hidden">
        <div class="card-header bg-secondary text-white fw-bold fs-5 d-flex align-items-center px-4 py-2">
            <i class="bi bi-geo-alt-fill me-3 fs-4"></i> Thửa - Tờ - Diện tích chung
        </div>
        <div class="card-body p-0">
            @php $thuaChung = $hoSo->thua_chung ?? []; @endphp
            @if (!empty($thuaChung) && is_array($thuaChung))
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="60">#</th>
                                <th>Tờ</th>
                                <th>Thửa</th>
                                <th>Diện tích (m²)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($thuaChung as $i => $t)
                                <tr>
                                    <td class="text-center fw-medium">{{ $i + 1 }}</td>
                                    <td class="fw-medium">{{ $t['to'] ?? '-' }}</td>
                                    <td class="fw-medium">{{ $t['thua'] ?? '-' }}</td>
                                    <td class="fw-medium">{{ $t['dien_tich'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted fst-italic fs-5">
                    Không có thông tin thửa chung
                </div>
            @endif
        </div>
    </div>

    <!-- Thông tin riêng -->
    <div class="card shadow border-0 mb-3 rounded-4 overflow-hidden">
        <div class="card-header bg-success text-white fw-bold fs-5 d-flex align-items-center px-4 py-2">
            <i class="bi bi-file-earmark-check-fill me-3 fs-4"></i> Thông tin riêng
        </div>
        <div class="card-body p-4">
            @php
                $rieng = $hoSo->thong_tin_rieng ?? [];
                $riengLoai = $rieng['loai'] ?? null;
                $riengData = $rieng['data'] ?? [];
                $riengThua = $riengData['thua'] ?? [];
            @endphp

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Loại thủ tục chi tiết</label>
                    <p class="fs-5 mb-0 fw-medium">
                        {{ $riengLoai ? str_replace('_', ' ', ucwords($riengLoai)) : 'Chưa chọn' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Họ tên</label>
                    <p class="fs-5 mb-0 fw-medium">{{ $riengData['ho_ten'] ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">CMND / CCCD</label>
                    <p class="fs-5 mb-0 fw-medium">{{ $riengData['cccd'] ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small">Ngày cấp CCCD / CMND</label>
                    <p class="fs-5 mb-0 fw-medium">
                        {{ $riengData['ngay_cap_cccd'] ? \Carbon\Carbon::parse($riengData['ngay_cap_cccd'])->format('d/m/Y') : '-' }}
                    </p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-muted small">Địa chỉ</label>
                    <p class="fs-5 mb-0 fw-medium">{{ $riengData['dia_chi'] ?? '-' }}</p>
                </div>
            </div>

            <h5 class="fw-bold text-success mb-4">Danh sách thửa đất chi tiết</h5>
            @if (!empty($riengThua) && is_array($riengThua))
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="60">#</th>
                                <th>Tờ</th>
                                <th>Thửa</th>
                                <th>Diện tích (m²)</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($riengThua as $i => $t)
                                <tr>
                                    <td class="text-center fw-medium">{{ $i + 1 }}</td>
                                    <td class="fw-medium">{{ $t['to'] ?? '-' }}</td>
                                    <td class="fw-medium">{{ $t['thua'] ?? '-' }}</td>
                                    <td class="fw-medium">{{ $t['dien_tich'] ?? '-' }}</td>
                                    <td class="fw-medium">{{ $t['ghi_chu'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center p-4 fs-5 mb-0 rounded-3">
                    Không có thông tin thửa đất chi tiết
                </div>
            @endif
        </div>
    </div>

    <!-- Ghi chú -->
    <div class="card shadow border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-warning text-dark fw-bold fs-5 d-flex align-items-center px-4 py-2">
            <i class="bi bi-journal-text me-3 fs-4"></i> Ghi chú
        </div>
        <div class="card-body p-4">
            <pre class="mb-0 bg-light p-4 rounded border fs-5 lh-lg" style="white-space: pre-wrap; word-wrap: break-word;">
                {{ $hoSo->ghi_chu ?? 'Không có ghi chú nào' }}
            </pre>
        </div>
    </div>
@endsection
