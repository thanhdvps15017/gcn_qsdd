@extends('welcome')

@section('title', 'Xuất Word hồ sơ')

@section('content')
    <style>
        .hover-scale {
            transition: all 0.2s ease-in-out;
        }
        .hover-scale:hover {
            transform: translateY(-1px);
        }
        .mau-item {
            transition: all 0.15s ease-in-out;
            border-left: 3px solid transparent;
        }
        .mau-item:hover {
            background-color: rgba(11, 95, 165, 0.05);
            border-left-color: rgba(11, 95, 165, 0.3);
        }
        .mau-item.active {
            background-color: rgba(11, 95, 165, 0.08) !important;
            color: var(--primary) !important;
            border-left-color: var(--primary) !important;
            font-weight: 600;
        }
        .hoso-row {
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
        }
        .hoso-row:hover {
            background-color: rgba(0, 0, 0, 0.02) !important;
        }
        .hoso-row.selected-row {
            background-color: rgba(11, 95, 165, 0.05) !important;
        }
        .fs-7 {
            font-size: 0.8rem;
        }
        .sticky-card {
            position: sticky;
            top: 1.5rem;
            z-index: 10;
        }
    </style>

    <!-- Page Header -->
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-word-fill text-primary fs-2"></i> Xuất Văn Bản Word
        </h3>
        <p class="text-muted mb-0">Chọn một mẫu tài liệu Word bên trái, chọn hồ sơ đất đai tương ứng bên phải để tạo văn bản nhanh.</p>
    </div>

    <div class="row g-4">

        <!-- CỘT TRÁI: MẪU WORD -->
        <div class="col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-card">
                <div class="card-header text-white fw-bold py-3"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <i class="bi bi-folder2-open me-2"></i>
                    CHỌN MẪU WORD
                </div>

                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="foldersAccordion">

                        @forelse ($folders as $index => $folder)
                            @if ($folder->mauWords->count() > 0)
                                <div class="accordion-item border-bottom">
                                    <!-- Header của folder -->
                                    <h2 class="accordion-header" id="heading-{{ $folder->id }}">
                                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} fw-bold py-3 text-dark bg-white"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{ $folder->id }}"
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse-{{ $folder->id }}">
                                            <i class="bi bi-folder-fill text-warning me-2"></i> {{ $folder->name }}
                                        </button>
                                    </h2>

                                    <!-- Nội dung folder: danh sách mẫu Word -->
                                    <div id="collapse-{{ $folder->id }}"
                                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                        aria-labelledby="heading-{{ $folder->id }}" data-bs-parent="#foldersAccordion">

                                        <div class="accordion-body p-0">
                                            <div class="list-group list-group-flush">
                                                @foreach ($folder->mauWords as $mau)
                                                    <button type="button"
                                                        class="list-group-item list-group-item-action mau-item px-3 py-2.5 border-0"
                                                        data-mau-id="{{ $mau->id }}"
                                                        data-mau-ten="{{ $mau->name }}"
                                                        data-mau-ghichu="{{ $mau->notes ?? '' }}"
                                                        data-mau-dinhkem="{{ $mau->attachment ?? '' }}">

                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="text-truncate me-2">
                                                                <i class="bi bi-file-earmark-word-fill text-primary me-1.5"></i>
                                                                <span class="small fw-semibold text-dark">{{ $mau->name }}</span>
                                                            </div>
                                                        </div>

                                                        <!-- Ghi chú ngắn -->
                                                        @if ($mau->notes)
                                                            <div class="mt-1 small text-secondary fs-7 text-truncate">
                                                                {{ $mau->notes }}
                                                            </div>
                                                        @endif
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                Chưa có mẫu Word nào
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: DANH SÁCH HỒ SƠ -->
        <div class="col-lg-8 col-xl-9">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <!-- Bộ lọc & Tìm kiếm -->
                <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-text text-primary"></i>
                        Chọn hồ sơ dữ liệu
                    </h5>

                    <form action="{{ route('xuat-word.index') }}" method="GET"
                        class="d-flex gap-2 flex-grow-1 flex-md-grow-0" style="max-width: 400px;">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Tìm mã HS, tên chủ, SĐT..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary px-3">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Thống kê & Nút hành động -->
                <div class="card-body bg-light-subtle border-bottom py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                            <span class="fw-semibold text-dark small">Mẫu Word đã chọn:</span>
                            <span id="mauSelectedDisplay" class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5 rounded-pill">Chưa chọn mẫu</span>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" id="btnPreview" class="btn btn-info px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm hover-scale" disabled>
                                <i class="bi bi-eye"></i> Xem trước (Preview)
                            </button>

                            <form id="exportForm" method="POST" action="{{ route('xuat-word.export') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="mau_word_id" id="hiddenMauId">
                                <input type="hidden" name="ho_so_id" id="hiddenHoSoId">

                                <button type="submit" id="btnExport" class="btn btn-success px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm hover-scale" disabled>
                                    <i class="bi bi-download"></i> Xuất Word
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bảng hồ sơ -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7 text-muted border-bottom">
                            <tr>
                                <th class="ps-4" style="width: 50px;"></th>
                                <th style="width: 80px;">STT</th>
                                <th>Mã hồ sơ</th>
                                <th>Tên chủ hồ sơ</th>
                                <th>Loại hồ sơ</th>
                                <th>Loại thủ tục</th>
                                <th class="pe-4">Xã / Phường</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hoSos as $index => $hs)
                                <tr class="hoso-row" data-id="{{ $hs->id }}">
                                    <td class="ps-4">
                                        <input type="radio" name="selected_hoso" class="check-hoso form-check-input border-secondary"
                                            value="{{ $hs->id }}" data-ma="{{ $hs->dossier_code ?? '—' }}"
                                            data-ten="{{ addslashes($hs->owner_name) }}">
                                    </td>
                                    <td class="text-muted fw-medium">{{ $index + $hoSos->firstItem() }}</td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $hs->dossier_code ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $hs->owner_name }}</div>
                                        @if($hs->owner_phone)
                                            <span class="text-muted small fs-7">
                                                <i class="bi bi-telephone small"></i> {{ $hs->owner_phone }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border border-secondary-subtle">
                                            {{ optional($hs->loaiHoSo)->name }}
                                        </span>
                                    </td>
                                    <td class="text-secondary small">{{ optional($hs->loaiThuTuc)->name }}</td>
                                    <td class="pe-4 text-muted small">
                                        <i class="bi bi-geo-alt"></i> {{ optional($hs->xa)->name ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                        Không tìm thấy hồ sơ nào phù hợp
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                @if($hoSos->hasPages())
                    <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Hiển thị {{ $hoSos->firstItem() }} đến {{ $hoSos->lastItem() }} trong tổng số {{ $hoSos->total() }} hồ sơ.
                        </div>
                        <div>
                            {{ $hoSos->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </div>

    <!-- Modal Preview -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-info text-white py-3 border-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="previewModalLabel">
                        <i class="bi bi-file-earmark-word"></i> Xem trước tài liệu Word
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-light" style="height: 75vh;">
                    <iframe id="previewIframe" src="" width="100%" height="100%" frameborder="0"></iframe>
                </div>
                <div class="modal-footer bg-white border-top-0 py-2.5">
                    <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let selectedMauId = null;
            const btnExport = document.getElementById('btnExport');
            const btnPreview = document.getElementById('btnPreview');
            const hiddenMau = document.getElementById('hiddenMauId');
            const hiddenHoSo = document.getElementById('hiddenHoSoId');
            const displayMau = document.getElementById('mauSelectedDisplay');

            // Chọn mẫu Word
            document.querySelectorAll('.mau-item').forEach(item => {
                item.addEventListener('click', function() {
                    document.querySelectorAll('.mau-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    selectedMauId = this.dataset.mauId;
                    hiddenMau.value = selectedMauId;
                    displayMau.textContent = this.dataset.mauTen;
                    displayMau.className = 'badge bg-primary text-white px-2.5 py-1.5 rounded-pill';

                    updateButtons();
                });
            });

            // Xử lý chọn hồ sơ (radio + click dòng)
            document.querySelectorAll('.hoso-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.tagName !== 'INPUT') {
                        const radio = this.querySelector('.check-hoso');
                        radio.checked = true;
                        
                        // Cập nhật class dòng được chọn
                        document.querySelectorAll('.hoso-row').forEach(r => r.classList.remove('selected-row'));
                        this.classList.add('selected-row');
                        
                        updateButtons();
                    }
                });
            });

            document.querySelectorAll('.check-hoso').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.hoso-row').forEach(r => r.classList.remove('selected-row'));
                    this.closest('.hoso-row').classList.add('selected-row');
                    updateButtons();
                });
            });

            function updateButtons() {
                const selectedRadio = document.querySelector('.check-hoso:checked');

                const hasMau = !!selectedMauId;
                const hasHoSo = !!selectedRadio;

                const disabled = !(hasMau && hasHoSo);

                btnExport.disabled = disabled;
                btnPreview.disabled = disabled;

                if (hasHoSo) {
                    hiddenHoSo.value = selectedRadio.value;
                } else {
                    hiddenHoSo.value = '';
                }
            }

            // Xử lý Preview
            btnPreview.addEventListener('click', async () => {
                const selected = document.querySelector('.check-hoso:checked');
                if (!selected) return;

                // Hiển thị loading nhẹ
                btnPreview.disabled = true;
                btnPreview.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang tạo...`;

                const formData = new FormData();
                formData.append('ho_so_id', selected.value);
                formData.append('mau_word_id', selectedMauId);

                try {
                    const res = await fetch('{{ route('xuat-word.preview') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();

                    if (data.success) {
                        const viewer = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(data.url)}`;
                        document.getElementById('previewIframe').src = viewer;
                        new bootstrap.Modal(document.getElementById('previewModal')).show();
                    } else {
                        alert(data.message || 'Lỗi khi tạo xem trước');
                    }
                } catch (err) {
                    alert('Lỗi kết nối: ' + err.message);
                } finally {
                    btnPreview.disabled = false;
                    btnPreview.innerHTML = `<i class="bi bi-eye"></i> Xem trước (Preview)`;
                }
            });

            // Khởi tạo ban đầu
            updateButtons();

        });
    </script>
@endpush
