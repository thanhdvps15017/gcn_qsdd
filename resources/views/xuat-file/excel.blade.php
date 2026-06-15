@extends('welcome')

@section('content')
    <style>
        .hover-scale {
            transition: all 0.2s ease-in-out;
        }
        .hover-scale:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.25) !important;
        }
        .filter-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .fs-7 {
            font-size: 0.8rem;
        }
        .table-responsive {
            border-radius: 12px;
        }
    </style>

    <!-- Header Block -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-excel-fill text-success fs-2"></i> Xuất Báo Cáo Excel
            </h3>
            <p class="text-muted mb-0">Lọc danh sách và trích xuất dữ liệu hồ sơ đất đai sang tệp Excel.</p>
        </div>
        <div>
            <a href="{{ route('xuat-excel.export', request()->query()) }}" 
               class="btn btn-success px-4 py-2.5 fw-semibold shadow-sm d-inline-flex align-items-center gap-2 hover-scale">
                <i class="bi bi-download"></i> Xuất Excel ({{ $hoSos->total() }} dòng)
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 filter-card mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-funnel text-primary"></i> Bộ lọc tìm kiếm
            </h5>
            
            <form method="GET" action="{{ route('xuat-excel.index') }}">
                <div class="row g-3">
                    <!-- Loại hồ sơ -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Loại hồ sơ</label>
                        <select name="dossier_type_id" class="form-select" data-container="body">
                            <option value="">Tất cả loại hồ sơ</option>
                            @foreach ($loaiHoSos as $item)
                                <option value="{{ $item->id }}" @selected(request('dossier_type_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Loại thủ tục -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Loại thủ tục</label>
                        <select name="procedure_type_id" class="form-select" data-container="body">
                            <option value="">Tất cả loại thủ tục</option>
                            @foreach ($loaiThuTucs as $item)
                                <option value="{{ $item->id }}" @selected(request('procedure_type_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Xã / Phường -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Xã / Phường</label>
                        <select name="ward_id" class="form-select" data-container="body">
                            <option value="">Tất cả xã / phường</option>
                            @foreach ($xas as $item)
                                <option value="{{ $item->id }}" @selected(request('ward_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Người thẩm tra -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Người thẩm tra</label>
                        <select name="inspector_id" class="form-select" data-container="body">
                            <option value="">Tất cả người thẩm tra</option>
                            @foreach ($users as $item)
                                <option value="{{ $item->id }}" @selected(request('inspector_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Từ ngày -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Từ ngày nhận</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" name="created_from" value="{{ request('created_from') }}" 
                                   class="form-control border-start-0 ps-0">
                        </div>
                    </div>

                    <!-- Đến ngày -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted small">Đến ngày nhận</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-calendar-check"></i>
                            </span>
                            <input type="date" name="created_to" value="{{ request('created_to') }}" 
                                   class="form-control border-start-0 ps-0">
                        </div>
                    </div>

                    <!-- Nút hành động -->
                    <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                        <a href="{{ route('xuat-excel.index') }}" class="btn btn-outline-secondary px-4 py-2">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-search"></i> Áp dụng bộ lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data List -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-muted border-bottom">
                        <tr>
                            <th class="ps-4" style="width: 80px;">STT</th>
                            <th>Mã hồ sơ</th>
                            <th>Chủ hồ sơ</th>
                            <th>Loại hồ sơ</th>
                            <th>Loại thủ tục</th>
                            <th>Xã / Phường</th>
                            <th>Người thẩm tra</th>
                            <th>Trạng thái</th>
                            <th class="pe-4" style="width: 140px;">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hoSos as $index => $hs)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">
                                    {{ $hoSos->firstItem() + $index }}
                                </td>
                                <td>
                                    <a href="{{ route('ho-so.show', $hs->id) }}" class="fw-bold text-decoration-none text-primary">
                                        {{ $hs->dossier_code }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $hs->owner_name }}</div>
                                    @if($hs->owner_phone)
                                        <span class="text-muted small">
                                            <i class="bi bi-telephone text-muted small"></i> {{ $hs->owner_phone }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border border-secondary-subtle">
                                        {{ optional($hs->loaiHoSo)->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark small">
                                        {{ optional($hs->loaiThuTuc)->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <i class="bi bi-geo-alt"></i> {{ optional($hs->xa)->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small fw-medium text-dark">
                                        {{ optional($hs->nguoiThamTra)->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $hs->trang_thai_meta['color'] ?? 'secondary' }} px-2.5 py-1.5">
                                        {{ $hs->trang_thai_meta['text'] ?? 'Chưa rõ' }}
                                    </span>
                                </td>
                                <td class="pe-4 text-muted small">
                                    {{ $hs->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                    Không tìm thấy hồ sơ nào khớp với bộ lọc
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($hoSos->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $hoSos->firstItem() }} đến {{ $hoSos->lastItem() }} trong tổng số {{ $hoSos->total() }} hồ sơ.
                </div>
                <div>
                    {{ $hoSos->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
@endsection
