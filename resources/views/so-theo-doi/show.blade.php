@extends('welcome')

@section('content')
    <!-- Header -->
    <div class="card-header text-white d-flex justify-content-between align-items-center rounded-1"
        style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
        <h5 class="mb-0 fw-bold">
            Sổ: {{ $group->book_code }} - {{ $group->book_name }}
        </h5>

        <a href="{{ route('so-theo-doi.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row g-4 pt-3">
        <!-- CỘT TRÁI -->
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-1 h-100">
                <div class="card-header bg-success text-white fw-bold">
                    Thêm hồ sơ vào sổ
                </div>

                <div class="card-body p-2">
                    <form method="POST" action="{{ route('so-theo-doi.batch-add', $group) }}" id="batch-add-form">
                        @csrf

                        <input type="text" id="search-chua-them" class="form-control mb-2"
                            placeholder="🔍 Tìm hồ sơ chưa thêm...">

                        <div class="border rounded p-3 bg-white" style="min-height: 350px; max-height: 400px; overflow-y: auto;">
                            <div id="list-chua-them-container">
                                @forelse ($hoSosChuaThem as $hs)
                                    <div class="form-check list-chua-them-item mb-2">
                                        <input class="form-check-input ho-so-check" type="checkbox" name="ho_so_ids[]" value="{{ $hs->id }}" id="hs-{{ $hs->id }}">
                                        <label class="form-check-label" for="hs-{{ $hs->id }}">
                                            <strong>{{ $hs->dossier_code }}</strong> - {{ $hs->owner_name ?? 'Không tên' }}
                                        </label>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-5" id="empty-chua-them-msg">
                                        Không còn hồ sơ nào chưa thêm
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <button class="btn btn-success w-100 mt-2">
                            <i class="bi bi-plus-lg"></i> Thêm hồ sơ
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI -->
        <div class="col-md-12">
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
                                        <th>Mã sổ theo dõi</th>
                                        <th>Mã hồ sơ</th>
                                        <th>Chủ hồ sơ</th>
                                        <th>Loại hồ sơ</th>
                                        <th>Loại thủ tục</th>
                                        <th>Người thẩm tra</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody id="table-trong-so">
                                    @foreach ($hoSosTrongSo as $hs)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ho_so_ids[]" value="{{ $hs->id }}">
                                            </td>
                                            <td>{{ $hs->pivot->order_index }}</td>
                                            <td>{{ $hs->dossier_code }}</td>
                                            <td>{{ $hs->owner_name }}</td>
                                            <td>{{ $hs->loaiHoSo->name }}</td>
                                            <td>{{ $hs->loaiThuTuc->name }}</td>
                                            <td>{{ $hs->nguoiThamTra->name }}</td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary btn-open-note"
                                                    data-ho-so-id="{{ $hs->id }}"
                                                    data-ghi-chu="{{ $hs->pivot->notes }}">
                                                    <i class="bi bi-journal-text"></i> Ghi chú
                                                </button>
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

                        <button class="btn btn-danger btn-sm mt-2" onclick="confirmDelete(event, document.getElementById('batch-remove-form'), 'Bạn chắc chắn muốn xóa các hồ sơ đã chọn khỏi sổ theo dõi?')">
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

    <div class="modal fade" id="ghiChuModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ghi chú xử lý hồ sơ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <textarea id="ghi-chu-text" class="form-control" rows="4" placeholder="Nhập ghi chú trong quá trình xử lý..."></textarea>

                    <input type="hidden" id="ghi-chu-ho-so-id">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" id="btn-save-ghi-chu">
                        Lưu ghi chú
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modalEl = document.getElementById('ghiChuModal');
            const ghiChuModal = new bootstrap.Modal(modalEl);

            document.querySelectorAll('.btn-open-note').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('ghi-chu-ho-so-id').value = this.dataset.hoSoId;

                    document.getElementById('ghi-chu-text').value = this.dataset.ghiChu || '';

                    ghiChuModal.show();
                });
            });


            document.getElementById('btn-save-ghi-chu').addEventListener('click', function() {
                const hoSoId = document.getElementById('ghi-chu-ho-so-id').value;
                const ghiChu = document.getElementById('ghi-chu-text').value;

                fetch(`/so-theo-doi/{{ $group->id }}/ho-so/${hoSoId}/ghi-chu`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            notes: ghiChu
                        })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error();
                        return res.json();
                    })
                    .then(() => {
                        ghiChuModal.hide();
                        showToast('✔ Lưu ghi chú thành công');

                        const btn = document.querySelector(
                            `.btn-open-note[data-ho-so-id="${hoSoId}"]`
                        );

                        if (btn) {
                            btn.dataset.ghiChu = ghiChu;
                        }
                    })
                    .catch(() => alert('Không thể lưu ghi chú'));
            });

            function showToast(message) {
                const toast = document.createElement('div');
                toast.className =
                    'toast align-items-center text-bg-success border-0 show position-fixed bottom-0 end-0 m-3';
                toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"></button>
            </div>
        `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2500);
            }

        });
    </script>

    {{-- ================= JS ================= --}}
    <script>
        // Store checked dossiers: id -> { dossier_code, owner_name }
        const checkedHoSos = new Map();

        // Listen for checkbox changes in the add form to keep track of selection
        const containerEl = document.getElementById('list-chua-them-container');
        if (containerEl) {
            containerEl.addEventListener('change', function(e) {
                if (e.target.classList.contains('ho-so-check')) {
                    const id = e.target.value;
                    const labelEl = e.target.nextElementSibling || e.target.parentElement.querySelector('label');
                    const code = labelEl.querySelector('strong').textContent;
                    const name = labelEl.textContent.replace(code, '').replace('-', '').trim();
                    
                    if (e.target.checked) {
                        checkedHoSos.set(id.toString(), { dossier_code: code, owner_name: name });
                    } else {
                        checkedHoSos.delete(id.toString());
                    }
                }
            });
        }

        // Validate batch add form submission
        document.getElementById('batch-add-form')?.addEventListener('submit', function(e) {
            const checkedCount = document.querySelectorAll('#list-chua-them-container input[type="checkbox"]:checked').length;
            if (checkedCount === 0) {
                e.preventDefault();
                Swal.fire('Chú ý', 'Vui lòng chọn ít nhất một hồ sơ để thêm vào sổ!', 'warning');
            }
        });

        /* CHECK ALL (Only for table-trong-so) */
        document.getElementById('check-all').addEventListener('click', e => {
            document.querySelectorAll('#table-trong-so input[name="ho_so_ids[]"]').forEach(cb => cb.checked = e.target.checked);
        });

        /* SEARCH CHƯA THÊM */
        document.getElementById('search-chua-them').addEventListener('input', function() {
            fetch(`{{ route('so-theo-doi.search-chua-them', $group) }}?q=${this.value}`)
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('list-chua-them-container');
                    container.innerHTML = '';

                    if (!data.length && checkedHoSos.size === 0) {
                        container.innerHTML = `
                            <div class="text-muted text-center py-5">
                                Không tìm thấy hồ sơ nào
                            </div>
                        `;
                        return;
                    }

                    const renderedIds = new Set();

                    // 1. Render matching items
                    data.forEach(hs => {
                        const idStr = hs.id.toString();
                        renderedIds.add(idStr);
                        const isChecked = checkedHoSos.has(idStr);
                        container.innerHTML += `
                            <div class="form-check list-chua-them-item mb-2">
                                <input class="form-check-input ho-so-check" type="checkbox" name="ho_so_ids[]" value="${hs.id}" id="hs-${hs.id}" ${isChecked ? 'checked' : ''}>
                                <label class="form-check-label" for="hs-${hs.id}">
                                    <strong>${hs.dossier_code}</strong> - ${hs.owner_name ?? 'Không tên'}
                                </label>
                            </div>
                        `;
                    });

                    // 2. Render checked items that weren't in the search results
                    checkedHoSos.forEach((info, id) => {
                        if (!renderedIds.has(id)) {
                            container.innerHTML += `
                                <div class="form-check list-chua-them-item mb-2">
                                    <input class="form-check-input ho-so-check" type="checkbox" name="ho_so_ids[]" value="${id}" id="hs-${id}" checked>
                                    <label class="form-check-label" for="hs-${id}">
                                        <strong>${info.dossier_code}</strong> - ${info.owner_name}
                                    </label>
                                </div>
                            `;
                        }
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
                        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">
                    Không tìm thấy hồ sơ
                </td></tr>`;
                        return;
                    }

                    data.forEach(hs => {
                        const orderIndexStr = hs.pivot?.order_index || hs.order_index || '-';
                        const notesStr = hs.pivot?.notes || hs.notes || '';
                        tbody.innerHTML += `
                <tr>
                    <td><input type="checkbox" name="ho_so_ids[]" value="${hs.id}"></td>
                    <td>${orderIndexStr}</td>
                    <td>${hs.dossier_code}</td>
                    <td>${hs.owner_name ?? '-'}</td>
                    <td>${hs.loai_ho_so?.name ?? hs.loaiHoSo?.name ?? '-'}</td>
                    <td>${hs.loai_thu_tuc?.name ?? hs.loaiThuTuc?.name ?? '-'}</td>
                    <td>${hs.nguoi_tham_tra?.name ?? hs.nguoiThamTra?.name ?? '-'}</td>
                    <td>
                        <button type="button"
                            class="btn btn-sm btn-outline-secondary btn-open-note"
                            data-ho-so-id="${hs.id}"
                            data-ghi-chu="${notesStr}">
                            <i class="bi bi-journal-text"></i> Ghi chú
                        </button>
                        <a href="/ho-so/${hs.id}" class="btn btn-sm btn-outline-primary">Xem</a>
                    </td>
                </tr>`;
                    });

                    // Re-bind note open events for newly rendered rows
                    bindNoteOpenEvents();
                });
        });

        function bindNoteOpenEvents() {
            document.querySelectorAll('.btn-open-note').forEach(btn => {
                // Remove existing event listener first to avoid duplication
                btn.replaceWith(btn.cloneNode(true));
            });
            
            // Re-select and bind
            document.querySelectorAll('.btn-open-note').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('ghi-chu-ho-so-id').value = this.dataset.hoSoId;
                    document.getElementById('ghi-chu-text').value = this.dataset.ghiChu || '';
                    const modalEl = document.getElementById('ghiChuModal');
                    const ghiChuModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    ghiChuModal.show();
                });
            });
        }
    </script>
@endsection
