@extends('welcome')

@section('title', 'Xuất Word hồ sơ')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- HEADER --}}
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
                    <h5 class="mb-0 fw-bold">DANH SÁCH HỒ SƠ – XUẤT WORD</h5>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive p-3 overflow-visible">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>MÃ HỒ SƠ</th>
                                <th>TÊN CHỦ HỒ SƠ</th>
                                <th class="d-none d-md-table-cell">XÃ</th>
                                <th width="5%" class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hoSos as $hs)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        {{ $hs->ma_ho_so }}
                                    </td>

                                    <td>
                                        {{ $hs->ten_chu_ho_so }}
                                    </td>

                                    <td class="d-none d-md-table-cell">
                                        {{ optional($hs->xa)->ten ?? '—' }}
                                    </td>

                                    {{-- ACTION --}}
                                    <td class="text-end position-static">
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalMau" data-id="{{ $hs->id }}">
                                            <i class="bi bi-file-earmark-word"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Chưa có hồ sơ
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="px-3 pb-3">
                    {{ $hoSos->links() }}
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL CHỌN MẪU WORD ================= --}}
    <div class="modal fade" id="modalMau" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="fw-bold mb-0">Chọn mẫu Word</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="{{ route('xuat-word.export') }}">
                    @csrf

                    <div class="modal-body p-4">

                        {{-- ID HỒ SƠ --}}
                        <input type="hidden" name="ho_so_id" id="ho_so_id">

                        {{-- CHỌN MẪU --}}
                        <div class="mb-3">
                            <label class="fw-bold mb-1">Mẫu Word *</label>
                            <select name="mau_word_id" class="form-select" required>
                                <option value="">-- Chọn mẫu --</option>
                                @foreach ($mauWords as $mau)
                                    <option value="{{ $mau->id }}">
                                        {{ $mau->ten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Huỷ
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-download"></i> Xuất Word
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalMau');

            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const hoSoId = button.getAttribute('data-id');
                document.getElementById('ho_so_id').value = hoSoId;
            });
        });
    </script>
@endpush
