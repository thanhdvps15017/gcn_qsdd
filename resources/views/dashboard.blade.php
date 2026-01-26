@extends('welcome')

@section('title', 'Dashboard - Quản lý hồ sơ')

@section('content')

    <div class="container-fluid">

        <!-- Thống kê nhanh (cards) -->
        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng hồ sơ
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tongHoSo }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-folder fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Hoàn thành
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $hoanThanh }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Sắp hết hạn (≤ 5 ngày)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sapHetHan }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Quá hạn
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $quaHan }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Hồ sơ cần xử lý gấp -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Hồ sơ sắp/quá hạn giải quyết</h6>
                        <a href="{{ route('ho-so.index', ['filter' => 'het_han']) }}"
                            class="btn btn-sm btn-primary">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Mã HS</th>
                                        <th>Chủ hồ sơ</th>
                                        <th>Trạng thái</th>
                                        <th>Hạn giải quyết</th>
                                        <th>Ngày còn lại</th>
                                        <th>Xã</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($hoSoGap as $hs)
                                        <tr>
                                            <td>{{ $hs->ma_ho_so }}</td>
                                            <td>{{ $hs->ten_chu_ho_so }}</td>
                                            <td>
                                                <span class="badge badge-{{ $hs->trang_thai_meta['color'] }}">
                                                    {{ $hs->trang_thai_meta['text'] }}
                                                </span>
                                            </td>
                                            <td>{{ $hs->han_giai_quyet?->format('d/m/Y') ?? '—' }}</td>
                                            <td
                                                class="{{ $hs->han_giai_quyet && now()->diffInDays($hs->han_giai_quyet, false) <= 0 ? 'text-danger font-weight-bold' : '' }}">
                                                {{ $hs->han_giai_quyet ? now()->diffForHumans($hs->han_giai_quyet) : '—' }}
                                            </td>
                                            <td>{{ $hs->xa?->ten ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('ho-so.show', $hs) }}"
                                                    class="btn btn-sm btn-info">Chi tiết</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Không có hồ sơ nào sắp/quá hạn</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Có thể thêm Chart.js pie chart trạng thái ở đây -->

    </div>

    @endsection@extends('layouts.admin')

    @section('title', 'Dashboard - Quản lý hồ sơ')

@section('content')

    <div class="container-fluid">

        <!-- Thống kê nhanh (cards) -->
        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng hồ sơ
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tongHoSo }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-folder fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Hoàn thành
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $hoanThanh }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Sắp hết hạn (≤ 5 ngày)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sapHetHan }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Quá hạn
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $quaHan }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Hồ sơ cần xử lý gấp -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Hồ sơ sắp/quá hạn giải quyết</h6>
                        <a href="{{ route('ho-so.index', ['filter' => 'het_han']) }}"
                            class="btn btn-sm btn-primary">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Mã HS</th>
                                        <th>Chủ hồ sơ</th>
                                        <th>Trạng thái</th>
                                        <th>Hạn giải quyết</th>
                                        <th>Ngày còn lại</th>
                                        <th>Xã</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($hoSoGap as $hs)
                                        <tr>
                                            <td>{{ $hs->ma_ho_so }}</td>
                                            <td>{{ $hs->ten_chu_ho_so }}</td>
                                            <td>
                                                <span class="badge badge-{{ $hs->trang_thai_meta['color'] }}">
                                                    {{ $hs->trang_thai_meta['text'] }}
                                                </span>
                                            </td>
                                            <td>{{ $hs->han_giai_quyet?->format('d/m/Y') ?? '—' }}</td>
                                            <td
                                                class="{{ $hs->han_giai_quyet && now()->diffInDays($hs->han_giai_quyet, false) <= 0 ? 'text-danger font-weight-bold' : '' }}">
                                                {{ $hs->han_giai_quyet ? now()->diffForHumans($hs->han_giai_quyet) : '—' }}
                                            </td>
                                            <td>{{ $hs->xa?->ten ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('ho-so.show', $hs) }}"
                                                    class="btn btn-sm btn-info">Chi tiết</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Không có hồ sơ nào sắp/quá hạn</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Có thể thêm Chart.js pie chart trạng thái ở đây -->

    </div>

@endsection
