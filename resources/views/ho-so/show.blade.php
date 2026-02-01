@extends('welcome')

@section('content')

    <!-- Header -->
    <div class="card-header text-white d-flex justify-content-between align-items-center mb-4 rounded-1"
        style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-file-earmark-text-fill me-3"></i>
            Hồ sơ: {{ $hoSo->ma_ho_so ?? '—' }}
        </h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('ho-so.index') }}" class="btn btn-light btn-md px-3">
                <i class="bi bi-arrow-left me-2"></i> Quay lại
            </a>
            <a href="{{ route('ho-so.edit', $hoSo) }}" class="btn btn-light btn-md px-3">
                <i class="bi bi-pencil-square me-2"></i> Sửa
            </a>
            <form action="{{ route('ho-so.destroy', $hoSo) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Bạn có chắc chắn muốn xóa hồ sơ này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-light btn-md px-3 text-danger">
                    <i class="bi bi-trash3 me-2"></i> Xóa
                </button>
            </form>
        </div>
    </div>

    <div class="row mb-4">

        <!-- CỘT TRÁI: TẤT CẢ THÔNG TIN CHÍNH -->
        <div class="col-lg-8">

            <!-- Thông tin chung -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold bg-light">
                    <i class="bi bi-info-circle-fill me-2"></i> Thông tin chung
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small text-muted">Tên chủ hồ sơ</label>
                            <p class="mb-0 fs-5 fw-medium">{{ $hoSo->ten_chu_ho_so ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted">SĐT</label>
                            <p class="mb-0">{{ $hoSo->sdt_chu_ho_so ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted">Người thẩm tra</label>
                            <p class="mb-0">{{ optional($hoSo->nguoiThamTra)->name ?? '—' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted">Loại hồ sơ</label>
                            <p class="mb-0">{{ optional($hoSo->loaiHoSo)->name ?? '—' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted">Loại thủ tục</label>
                            <p class="mb-0">{{ optional($hoSo->loaiThuTuc)->name ?? '—' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted">Hạn giải quyết</label>
                            <p class="mb-0">
                                {{ $hoSo->han_giai_quyet ? $hoSo->han_giai_quyet->format('d/m/Y') : '—' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted">Xã / Phường</label>
                            <p class="mb-0">{{ optional($hoSo->xa)->name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chủ sử dụng -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold bg-light">
                    <i class="bi bi-person-badge-fill me-2"></i> Chủ sử dụng (Theo GCN)
                </div>
                <div class="card-body">
                    @php
                        $chuList = is_array($hoSo->chu_su_dung)
                            ? $hoSo->chu_su_dung
                            : ($hoSo->chu_su_dung
                                ? [$hoSo->chu_su_dung]
                                : []);
                    @endphp
                    @forelse ($chuList as $chu)
                        <div class="border rounded p-3 bg-light mb-3">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="small text-muted">Họ tên</label>
                                    <p class="mb-0 fw-medium">
                                        {{ $chu['xung_ho'] ?? '' }} {{ $chu['ho_ten'] ?? '—' }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted">Ngày sinh</label>
                                    <p class="mb-0">
                                        {{ !empty($chu['ngay_sinh']) ? \Carbon\Carbon::parse($chu['ngay_sinh'])->format('d/m/Y') : '—' }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted">CCCD/CMND</label>
                                    <p class="mb-0">{{ $chu['cccd'] ?? '—' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted">Ngày cấp</label>
                                    <p class="mb-0">
                                        {{ !empty($chu['ngay_cap']) ? \Carbon\Carbon::parse($chu['ngay_cap'])->format('d/m/Y') : '—' }}
                                    </p>
                                </div>
                                <div class="col-12">
                                    <label class="small text-muted">Địa chỉ</label>
                                    <p class="mb-0">{{ $chu['dia_chi'] ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">Không có thông tin chủ sử dụng</p>
                    @endforelse
                </div>
                @php $uy = $hoSo->uy_quyen ?? []; @endphp
                @if (!empty($uy['nguoi']) || !empty($uy['giay']))
                    <div class="card-footer bg-light border-0">
                        <div class="row g-3 small text-muted">
                            <div class="col-md-4">
                                <strong>Người ủy quyền:</strong>
                                {{ $uy['nguoi'] ?? '—' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Giấy ủy quyền:</strong>
                                {{ $uy['giay'] ?? '—' }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Thửa đất chung -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold bg-light">
                    <i class="bi bi-geo-alt-fill me-2"></i> Thửa - tờ - diện tích
                </div>
                <div class="card-body p-0">
                    @php $thuaChung = $hoSo->thua_chung ?? []; @endphp
                    @if (!empty($thuaChung))
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="50">#</th>
                                        <th>Tờ</th>
                                        <th>Thửa</th>
                                        <th>Diện tích (m²)</th>
                                        <th>Xã</th>
                                        <th>Ấp / Thôn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($thuaChung as $i => $t)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>{{ $t['to'] ?? '—' }}</td>
                                            <td>{{ $t['thua'] ?? '—' }}</td>
                                            <td>{{ $t['dien_tich'] ?? '—' }}</td>
                                            <td>{{ optional($t['xa_id'])->name ?? ($t['xa_id'] ? '—' : '—') }}</td>
                                            <td>{{ $t['ap_thon'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">Không có thông tin thửa đất chung</div>
                    @endif
                </div>
                <div class="card-footer bg-light border-0">
                    <div class="row g-3 small text-muted">
                        <div class="col-md-4">
                            <strong>Ngày cấp GCN:</strong>
                            {{ $hoSo->ngay_cap_gcn ? $hoSo->ngay_cap_gcn->format('d/m/Y') : '—' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Số vào sổ:</strong> {{ $hoSo->so_vao_so ?? '—' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Số phát hành:</strong> {{ $hoSo->so_phat_hanh ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin sau biến động -->
            <div class="card shadow-sm">
                <div class="card-header fw-bold bg-light">
                    <i class="bi bi-file-earmark-check-fill me-2"></i> Thông tin sau biến động
                </div>
                <div class="card-body">
                    @php
                        $rieng = $hoSo->thong_tin_rieng ?? [];
                        $loai = $rieng['loai'] ?? null;
                        $data = $rieng['data'] ?? [];
                        $nguoiLienQuan = $data['nguoi_lien_quan'] ?? [];
                        $thuaRieng = $data['thua'] ?? [];
                        $loaiMap = [
                            'tachthua_chuyennhuong' => 'Tách thửa - chuyển nhượng',
                            'capdoi' => 'Cấp đổi',
                            'chuyennhuong' => 'Chuyển nhượng',
                            'tachthua' => 'Tách thửa',
                            'capdoi_chuyennhuong' => 'Cấp đổi + chuyển nhượng',
                        ];
                    @endphp

                    <div class="mb-4">
                        <label class="small text-muted fw-semibold">Loại biến động</label>
                        <p class="fs-5 mb-0 fw-medium">{{ $loaiMap[$loai] ?? '—' }}</p>
                    </div>

                    <h6 class="fw-bold mb-3">Người liên quan / Bên nhận chuyển nhượng</h6>
                    @if (!empty($nguoiLienQuan))
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Họ tên</th>
                                        <th>CCCD</th>
                                        <th>Ngày cấp</th>
                                        <th>Địa chỉ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nguoiLienQuan as $i => $ng)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>{{ $ng['ho_ten'] ?? '—' }}</td>
                                            <td>{{ $ng['cccd'] ?? '—' }}</td>
                                            <td>{{ !empty($ng['ngay_cap_cccd']) ? \Carbon\Carbon::parse($ng['ngay_cap_cccd'])->format('d/m/Y') : '—' }}
                                            </td>
                                            <td>{{ $ng['dia_chi'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small mb-4">Không có thông tin người liên quan</p>
                    @endif

                    <h6 class="fw-bold mb-3">Thửa đất sau biến động</h6>
                    @if (!empty($thuaRieng))
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tờ</th>
                                        <th>Thửa</th>
                                        <th>Diện tích (m²)</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($thuaRieng as $i => $t)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>{{ $t['to'] ?? '—' }}</td>
                                            <td>{{ $t['thua'] ?? '—' }}</td>
                                            <td>{{ $t['dien_tich'] ?? '—' }}</td>
                                            <td>{{ $t['ghi_chu'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small">Không có thông tin thửa đất sau biến động</p>
                    @endif
                </div>
            </div>

        </div>

        <!-- CỘT PHẢI: GHI CHÚ + FILE + LỊCH SỬ (có collapse) -->
        <div class="col-lg-4">

            <!-- Ghi chú -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold bg-light d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse" data-bs-target="#collapseGhiChu" role="button">
                    <div>
                        <i class="bi bi-journal-text me-2"></i> Ghi chú
                    </div>
                    <span class="toggle-icon">−</span>
                </div>
                <div id="collapseGhiChu" class="collapse show">
                    <div class="card-body">
                        <div class="p-3 bg-light rounded border">
                            {{ $hoSo->ghi_chu ?: 'Không có ghi chú nào' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tài liệu đính kèm -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold bg-light d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse" data-bs-target="#collapseFiles" role="button">
                    <div>
                        <i class="bi bi-paperclip me-2"></i> Tài liệu đính kèm
                    </div>
                    <span class="toggle-icon">−</span>
                </div>
                <div id="collapseFiles" class="collapse show">
                    <div class="card-body">
                        @if ($hoSo->files->count())
                            <ul class="list-group list-group-flush">
                                @foreach ($hoSo->files as $file)
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                        <a href="{{ asset('storage/' . $file->duong_dan) }}" target="_blank"
                                            class="text-decoration-none fw-medium">
                                            {{ $file->ten_file }}
                                        </a>
                                        <!-- Nếu muốn giữ nút xóa ở trang show thì uncomment -->
                                        <!-- <button type="button" class="btn btn-sm btn-danger btn-delete-file"
                                                                        data-url="{{ route('ho-so.files.destroy', [$hoSo, $file]) }}"
                                                                        data-id="{{ $file->id }}">Xóa</button> -->
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted small mb-0">Chưa có tài liệu đính kèm</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lịch sử trạng thái -->
            <div class="card shadow-sm">
                <div class="card-header fw-bold bg-light d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse" data-bs-target="#collapseLichSu" role="button">
                    <div>
                        <i class="bi bi-clock-history me-2"></i> Lịch sử trạng thái
                    </div>
                    <span class="toggle-icon">−</span>
                </div>
                <div id="collapseLichSu" class="collapse show">
                    <div class="card-body timeline-scroll p-4">
                        @forelse ($hoSo->trangThaiLogs as $log)
                            @php
                                $statusMap = [
                                    'dang_giai_quyet' => ['Đang giải quyết', 'bg-warning text-dark'],
                                    'cho_bo_sung' => ['Chờ bổ sung', 'bg-info text-white'],
                                    'khong_du_dieu_kien' => ['Không đủ điều kiện', 'bg-danger text-white'],
                                    'chuyen_thue' => ['Chuyển thuế', 'bg-primary text-white'],
                                    'hs_niem_yet_xa' => ['Niêm yết xã', 'bg-secondary text-white'],
                                    'phoi_hop_do_dac' => ['Phối hợp đo đạc', 'bg-purple text-white'],
                                    'co_phieu_bao' => ['Có phiếu báo', 'bg-success text-white'],
                                    'in_gcn_qsdd' => ['In GCN QSDĐ', 'bg-dark text-white'],
                                ];
                                [$oldText, $oldClass] = $statusMap[$log->trang_thai_cu] ?? ['—', 'bg-light text-dark'];
                                [$newText, $newClass] = $statusMap[$log->trang_thai_moi] ?? [
                                    str_replace('_', ' ', ucwords($log->trang_thai_moi)),
                                    'bg-primary text-white',
                                ];
                            @endphp

                            <div class="timeline-item position-relative ps-4 pb-4">
                                <div class="timeline-dot position-absolute top-0 start-0 translate-middle-x rounded-circle border border-3 border-white {{ $newClass }}"
                                    style="width:14px;height:14px;z-index:1"></div>

                                @if (!$loop->last)
                                    <div class="timeline-line position-absolute top-0 start-0 bottom-0 bg-light"
                                        style="width:2px;left:6px"></div>
                                @endif

                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center gap-3 mb-1 flex-wrap">
                                        <strong>{{ $log->created_at->format('d/m/Y H:i') }}</strong>
                                        <div class="d-flex align-items-center gap-2">
                                            <span
                                                class="badge rounded-pill px-3 py-2 {{ $oldClass }}">{{ $oldText }}</span>
                                            <i class="bi bi-arrow-right-short fs-4 text-muted"></i>
                                            <span
                                                class="badge rounded-pill px-3 py-2 {{ $newClass }}">{{ $newText }}</span>
                                        </div>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="bi bi-person-circle me-1"></i>
                                        {{ $log->user->name ?? 'Hệ thống' }}
                                    </div>
                                    @if ($log->ghi_chu)
                                        <div class="fst-italic text-secondary small bg-light p-2 rounded border">
                                            “{{ $log->ghi_chu }}”
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-journal-x fs-1 opacity-50 d-block mb-3"></i>
                                Chưa có lịch sử trạng thái
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .timeline-scroll {
            max-height: 700px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .timeline-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .timeline-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }

        .card-header[role="button"]:hover {
            background-color: #f1f3f5 !important;
            cursor: pointer;
        }

        .toggle-icon {
            font-size: 1.4rem;
            font-weight: bold;
            transition: transform 0.3s;
        }

        .collapse.show+.card-header .toggle-icon {
            transform: rotate(0deg);
        }

        .collapse:not(.show)+.card-header .toggle-icon {
            transform: rotate(180deg);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý icon toggle khi collapse
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(header => {
                header.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-bs-target');
                    const target = document.querySelector(targetId);
                    const icon = this.querySelector('.toggle-icon');

                    if (target.classList.contains('show')) {
                        icon.textContent = '−';
                    } else {
                        icon.textContent = '+';
                    }
                });
            });

            // Nếu bạn muốn giữ chức năng xóa file ở trang show
            document.querySelectorAll('.btn-delete-file').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!confirm('Xóa file này?')) return;
                    const url = this.dataset.url;
                    const fileId = this.dataset.id;

                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Lỗi xóa');
                            return res.json();
                        })
                        .then(() => {
                            document.getElementById('file-row-' + fileId)?.remove();
                        })
                        .catch(() => alert('Không thể xóa file'));
                });
            });
        });
    </script>

@endsection
