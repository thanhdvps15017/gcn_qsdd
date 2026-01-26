@extends('welcome')

@section('title', 'Dashboard - Quản lý Hồ sơ')

@section('content')

    <div class="container-fluid">

        <!-- Thống kê nhanh (Cards) -->
        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng hồ sơ</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tongHoSo }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-folder fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hoàn thành</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $hoanThanh }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sắp hết hạn (≤5 ngày)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sapHetHan }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Quá hạn</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $quaHan }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">

            <!-- Top người thẩm tra -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top người thẩm tra (hồ sơ đang xử lý)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>STT</th>
                                        <th>Người thẩm tra</th>
                                        <th>Tổng HS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topNguoiThamTra as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->nguoiThamTra?->name ?? 'Chưa phân công' }}</td>
                                            <td class="font-weight-bold">{{ $item->tong }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Theo xã/phường -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top xã/phường có nhiều hồ sơ</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>STT</th>
                                        <th>Xã/Phường</th>
                                        <th>Số lượng HS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($theoXa as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->xa?->ten ?? '—' }}</td>
                                            <td class="font-weight-bold">{{ $item->tong }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Hồ sơ sắp/quá hạn (ưu tiên cao nhất) -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Hồ sơ cần xử lý gấp (hạn gần nhất)</h6>
                        <a href="{{ route('admin.hoso.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Mã HS</th>
                                        <th>Chủ HS</th>
                                        <th>Trạng thái</th>
                                        <th>Hạn giải quyết</th>
                                        <th>Còn lại</th>
                                        <th>Xã</th>
                                        <th>Người thẩm tra</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($hoSoGap as $hs)
                                        <tr
                                            class="{{ $hs->han_giai_quyet && $hs->han_giai_quyet->isPast() ? 'table-danger' : '' }}">
                                            <td>{{ $hs->ma_ho_so }}</td>
                                            <td>{{ $hs->ten_chu_ho_so }}</td>
                                            <td>
                                                <span class="badge bg-{{ $hs->trang_thai_meta['color'] }}">
                                                    {{ $hs->trang_thai_meta['text'] }}
                                                </span>
                                            </td>
                                            <td>{{ $hs->han_giai_quyet?->format('d/m/Y') ?? '—' }}</td>
                                            <td class="fw-bold">
                                                {{ $hs->han_giai_quyet ? $hs->han_giai_quyet->diffForHumans() : '—' }}
                                            </td>
                                            <td>{{ $hs->xa?->ten ?? '—' }}</td>
                                            <td>{{ $hs->nguoiThamTra?->name ?? 'Chưa phân' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">Không có hồ sơ gấp nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Theo trạng thái (pie chart hoặc bảng) -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Phân bố theo trạng thái</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($theoTrangThai as $tt)
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-{{ $tt->color }} me-2"
                                            style="width: 20px; height: 20px; border-radius: 50%;"></span>
                                        <strong>{{ $tt->text }}</strong>
                                        <span class="ms-auto badge bg-secondary">{{ $tt->tong }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
