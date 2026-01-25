@extends('welcome')

@section('title', 'Quản lý xã')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">DANH SÁCH XÃ / PHƯỜNG</h5>

                    <button class="btn btn-light btn-sm" onclick="openCreateXa()">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </button>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive p-3 overflow-visible">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>TÊN XÃ / PHƯỜNG</th>
                                <th width="5%" class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $item->name }}</td>

                                    <td class="text-end position-static">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-2" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <button class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                    onclick="openEditXa({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                                    <i class="bi bi-pencil-square"></i>
                                                    Chỉnh sửa
                                                </button>

                                                <form action="{{ route('xa.destroy', $item) }}" method="POST"
                                                    onsubmit="return confirm('Xoá xã {{ addslashes($item->name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                        <i class="bi bi-trash"></i>
                                                        Xoá
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        Chưa có dữ liệu
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL CREATE / EDIT ================= --}}
    <div class="modal fade" id="xaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="fw-bold" id="xaModalTitle"></h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="xaForm" method="POST">
                    @csrf
                    <input type="hidden" id="xaMethod">

                    <div class="modal-body p-4">
                        <label class="fw-bold">Tên xã / phường *</label>
                        <input name="name" id="xaName" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>

                        @error('name')
                            <div class="invalid-feedback d-block mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                        <button class="btn btn-primary">Lưu</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        const xaModal = new bootstrap.Modal(document.getElementById('xaModal'));
        const xaForm = document.getElementById('xaForm');

        function openCreateXa() {
            xaForm.reset();
            xaForm.action = "{{ route('xa.store') }}";
            document.getElementById('xaMethod').innerHTML = '';
            document.getElementById('xaModalTitle').innerText = 'Thêm xã / phường';
            xaModal.show();
        }

        function openEditXa(id, name) {
            xaForm.reset();
            xaForm.action = `/settings/xa/${id}`;
            document.getElementById('xaMethod').innerHTML =
                '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('xaModalTitle').innerText = 'Cập nhật xã / phường';
            document.getElementById('xaName').value = name;
            xaModal.show();
        }

        // Tự mở modal khi validate lỗi
        @if ($errors->has('name'))
            document.addEventListener('DOMContentLoaded', () => {
                openCreateXa();
            });
        @endif
    </script>
@endpush
