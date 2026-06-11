@extends('welcome')

@section('title', 'Xuất Word hồ sơ')

@section('content')

    <div class="row g-3">

        <!-- CỘT TRÁI: MẪU WORD -->
        <div class="col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 position-sticky" style="top: 1rem;">
                <div class="card-header text-white fw-bold"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <i class="bi bi-folder2-open me-2"></i>
                    CHỌN MẪU WORD
                </div>

                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="foldersAccordion">

                        @forelse ($folders as $index => $folder)
                            @if ($folder->mauWords->count() > 0)
                                <div class="accordion-item">
                                    <!-- Header của folder (click để mở/rút gọn) -->
                                    <h2 class="accordion-header" id="heading-{{ $folder->id }}">
                                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} fw-bold"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{ $folder->id }}"
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse-{{ $folder->id }}">
                                            📁 {{ $folder->name }}
                                        </button>
                                    </h2>

                                    <!-- Nội dung collapse: danh sách mẫu Word -->
                                    <div id="collapse-{{ $folder->id }}"
                                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                        aria-labelledby="heading-{{ $folder->id }}" data-bs-parent="#foldersAccordion">

                                        <div class="accordion-body p-0">
                                            <div class="list-group list-group-flush">
                                                @foreach ($folder->mauWords as $mau)
                                                    <button type="button"
                                                        class="list-group-item list-group-item-action mau-item px-4 py-3"
                                                        data-mau-id="{{ $mau->id }}"
                                                        data-mau-ten="{{ $mau->name }}"
                                                        data-mau-ghichu="{{ $mau->notes ?? '' }}"
                                                        data-mau-dinhkem="{{ $mau->attachment ?? '' }}">

                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="me-3">
                                                                <i
                                                                    class="bi bi-file-earmark-word-fill text-primary fs-4 me-2"></i>
                                                                <strong>{{ $mau->name }}</strong>
                                                            </div>
                                                            <small class="text-muted text-end">
                                                                {{ $mau->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>

                                                        <!-- Ghi chú -->
                                                        @if ($mau->notes)
                                                            <div class="mt-1 small text-secondary">
                                                                <i class="bi bi-journal-text me-1"></i>
                                                                {{ Str::limit($mau->notes, 90) }}
                                                            </div>
                                                        @endif

                                                        <!-- File đính kèm -->
                                                        @if ($mau->attachment)
                                                            <div class="mt-1 small">
                                                                <i class="bi bi-paperclip me-1 text-info"></i>
                                                                <a href="{{ Storage::url($mau->attachment) }}"
                                                                    target="_blank" class="text-info text-decoration-none"
                                                                    onclick="event.stopPropagation();">
                                                                    File đính kèm
                                                                </a>
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
                            <div class="list-group-item text-center text-muted py-4">
                                Chưa có mẫu Word nào
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: DANH SÁCH HỒ SƠ -->
        <div class="col-lg-8 col-xl-9">

            <div class="card border-0 shadow-sm rounded-3">

                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3"
                    style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">

                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        DANH SÁCH HỒ SƠ
                    </h5>

                    <form action="{{ route('xuat-word.index') }}" method="GET"
                        class="d-flex gap-2 flex-grow-1 flex-md-grow-0" style="max-width: 420px;">
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm mã HS, tên chủ, CCCD, SĐT..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary px-3"><i class="bi bi-search"></i></button>
                    </form>

                </div>

                <!-- PHẦN CHỌN MẪU + NÚT PREVIEW / XUẤT -->
                <div class="card-body border-bottom py-3">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 250px;">
                            <label class="fw-bold text-nowrap">Mẫu đã chọn:</label>
                            <span id="mauSelectedDisplay" class="text-muted">Chưa chọn mẫu</span>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" id="btnPreview" class="btn btn-info" disabled>
                                <i class="bi bi-eye me-1"></i> Preview
                            </button>

                            <form id="exportForm" method="POST" action="{{ route('xuat-word.export') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="mau_word_id" id="hiddenMauId">
                                <input type="hidden" name="ho_so_id" id="hiddenHoSoId">

                                <button type="submit" id="btnExport" class="btn btn-success" disabled>
                                    <i class="bi bi-download me-1"></i> Xuất Word
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="6%"></th> <!-- Bỏ checkbox chọn tất cả -->
                                <th>#</th>
                                <th>MÃ HỒ SƠ</th>
                                <th>TÊN CHỦ HỒ SƠ</th>
                                <th class="d-none d-md-table-cell">XÃ / PHƯỜNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hoSos as $hs)
                                <tr class="hoso-row" data-id="{{ $hs->id }}">
                                    <td>
                                        <input type="radio" name="selected_hoso" class="check-hoso form-check-input"
                                            value="{{ $hs->id }}" data-ma="{{ $hs->dossier_code ?? '—' }}"
                                            data-ten="{{ addslashes($hs->owner_name) }}">
                                    </td>
                                    <td>{{ $loop->iteration + ($hoSos->currentPage() - 1) * $hoSos->perPage() }}</td>
                                    <td class="fw-bold">{{ $hs->dossier_code ?? '—' }}</td>
                                    <td>{{ $hs->owner_name }}</td>
                                    <td class="d-none d-md-table-cell">{{ optional($hs->xa)->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        Không tìm thấy hồ sơ nào phù hợp
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-3">
                    {{ $hoSos->appends(request()->query())->links() }}
                    <small class="text-muted">
                        Hiển thị {{ $hoSos->count() }} / {{ $hoSos->total() }} hồ sơ
                    </small>
                </div>

            </div>

        </div>

    </div>

    <!-- Modal Preview -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="previewModalLabel">Xem trước tài liệu Word</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="height: 70vh;">
                    <iframe id="previewIframe" src="" width="100%" height="100%" frameborder="0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
                    document.querySelectorAll('.mau-item').forEach(i => i.classList.remove('active',
                        'list-group-item-primary'));
                    this.classList.add('active', 'list-group-item-primary');

                    selectedMauId = this.dataset.mauId;
                    hiddenMau.value = selectedMauId;
                    displayMau.textContent = this.dataset.mauTen;
                    displayMau.classList.remove('text-muted');
                    displayMau.classList.add('text-primary', 'fw-bold');

                    updateButtons();
                });
            });

            // Xử lý chọn hồ sơ (radio + click dòng)
            document.querySelectorAll('.hoso-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.tagName !== 'INPUT') {
                        const radio = this.querySelector('.check-hoso');
                        radio.checked = true;
                        updateButtons();
                    }
                });
            });

            document.querySelectorAll('.check-hoso').forEach(radio => {
                radio.addEventListener('change', updateButtons);
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

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
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
                        const viewer =
                            `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(data.url)}`;
                        document.getElementById('previewIframe').src = viewer;
                        new bootstrap.Modal(document.getElementById('previewModal')).show();
                    } else {
                        alert(data.message || 'Lỗi khi tạo preview');
                    }
                } catch (err) {
                    alert('Lỗi kết nối: ' + err.message);
                }
            });

            // Khởi tạo ban đầu
            updateButtons();

        });
    </script>
@endpush
