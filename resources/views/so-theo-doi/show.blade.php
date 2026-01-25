@extends('welcome')

@section('content')
    <!-- Header -->
    <div class="card-header text-white d-flex justify-content-between align-items-center rounded-1"
        style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
        <h5 class="mb-0 fw-bold">
            Sổ: {{ $group->ma_so }} - {{ $group->ten_so }}
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
                    <form method="POST" action="{{ route('so-theo-doi.batch-add', $group) }}">
                        @csrf

                        <input type="text" id="search-chua-them" class="form-control mb-2"
                            placeholder="🔍 Tìm hồ sơ chưa thêm...">

                        <select name="ho_so_ids[]" id="list-chua-them" class="form-select" multiple required
                            style="min-height:350px">
                            @foreach ($hoSosChuaThem as $hs)
                                <option value="{{ $hs->id }}">
                                    {{ $hs->ma_ho_so }} - {{ $hs->ten_chu_ho_so ?? 'Không tên' }}
                                </option>
                            @endforeach
                        </select>

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
                                        <th>Mã HS</th>
                                        <th>Mã STT</th>
                                        <th>Chủ HS</th>
                                        <th>Loại HS</th>
                                        <th>Loại TT</th>
                                        <th>Người TT</th>
                                        <th>Ghi chú</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody id="table-trong-so">
                                    @foreach ($hoSosTrongSo as $hs)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ho_so_ids[]" value="{{ $hs->id }}">
                                            </td>
                                            <td>{{ $hs->ma_ho_so }}</td>
                                            <td>{{ $hs->pivot->thu_tu }}</td>
                                            <td>{{ $hs->ten_chu_ho_so }}</td>
                                            <td>{{ $hs->loaiHoSo->name }}</td>
                                            <td>{{ $hs->loaiThuTuc->name }}</td>
                                            <td>{{ $hs->nguoiThamTra->name }}</td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary btn-open-note"
                                                    data-ho-so-id="{{ $hs->id }}"
                                                    data-ghi-chu="{{ $hs->pivot->ghi_chu }}">
                                                    <i class="bi bi-journal-text"></i>
                                                </button>
                                            </td>
                                            <td>
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

                        <button class="btn btn-danger btn-sm mt-2" onclick="return confirm('Xóa hồ sơ đã chọn?')">
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
                            ghi_chu: ghiChu
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
        /* CHECK ALL */
        document.getElementById('check-all').addEventListener('click', e => {
            document.querySelectorAll('input[name="ho_so_ids[]"]').forEach(cb => cb.checked = e.target.checked);
        });

        /* SEARCH CHƯA THÊM */
        document.getElementById('search-chua-them').addEventListener('input', function() {
            fetch(`{{ route('so-theo-doi.search-chua-them', $group) }}?q=${this.value}`)
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('list-chua-them');
                    select.innerHTML = '';
                    data.forEach(hs => {
                        select.innerHTML += `
                    <option value="${hs.id}">
                        ${hs.ma_ho_so} - ${hs.ten_chu_ho_so ?? 'Không tên'}
                    </option>`;
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
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">
                    Không tìm thấy hồ sơ
                </td></tr>`;
                        return;
                    }

                    data.forEach(hs => {
                        tbody.innerHTML += `
                <tr>
                    <td><input type="checkbox" name="ho_so_ids[]" value="${hs.id}"></td>
                    <td>${hs.ma_ho_so}</td>
                    <td>${hs.chu_su_dung?.ho_ten ?? '-'}</td>
                    <td>${hs.trang_thai ?? ''}</td>
                    <td>
                        <a href="/ho-so/${hs.id}" class="btn btn-sm btn-outline-primary">Xem</a>
                    </td>
                </tr>`;
                    });
                });
        });
    </script>
@endsection
