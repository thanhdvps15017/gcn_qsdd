@extends('welcome')

@section('content')
    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center"
            style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
            <h5 class="mb-0 fw-bold">Xuất Excel hồ sơ</h5>
            <a href="{{ route('xuat-excel.export', request()->query()) }}" class="btn btn-light btn-sm">
                Xuất Excel
            </a>
        </div>

        <div class="card-body">

            {{-- FILTER --}}
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <select name="loai_ho_so_id" class="form-control">
                            <option value="">-- Loại hồ sơ --</option>
                            @foreach ($loaiHoSos as $item)
                                <option value="{{ $item->id }}" @selected(request('loai_ho_so_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <select name="loai_thu_tuc_id" class="form-control">
                            <option value="">-- Loại thủ tục --</option>
                            @foreach ($loaiThuTucs as $item)
                                <option value="{{ $item->id }}" @selected(request('loai_thu_tuc_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <select name="xa_id" class="form-control">
                            <option value="">-- Xã --</option>
                            @foreach ($xas as $item)
                                <option value="{{ $item->id }}" @selected(request('xa_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <select name="nguoi_tham_tra_id" class="form-control">
                            <option value="">-- Người thẩm tra --</option>
                            @foreach ($users as $item)
                                <option value="{{ $item->id }}" @selected(request('nguoi_tham_tra_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <input type="date" name="created_from" value="{{ request('created_from') }}"
                            class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <input type="date" name="created_to" value="{{ request('created_to') }}" class="form-control">
                    </div>

                    <div class="col-md-12 mt-2">
                        <button class="btn btn-info w-100">Lọc</button>
                    </div>
                </div>
            </form>

            {{-- LIST --}}
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã hồ sơ</th>
                        <th>Loại hồ sơ</th>
                        <th>Loại thủ tục</th>
                        <th>Xã / Phường</th>
                        <th>Người thẩm tra</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hoSos as $index => $hs)
                        <tr>
                            <td>{{ $hoSos->firstItem() + $index }}</td>
                            <td>{{ $hs->ma_ho_so }}</td>
                            <td>{{ optional($hs->loaiHoSo)->name }}</td>
                            <td>{{ optional($hs->loaiThuTuc)->name }}</td>
                            <td>{{ optional($hs->xa)->name }}</td>
                            <td>{{ optional($hs->nguoiThamTra)->name }}</td>
                            <td>{{ $hs->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $hoSos->links() }}
        </div>
    </div>
@endsection
