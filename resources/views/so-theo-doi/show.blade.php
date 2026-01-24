@extends('welcome')

@section('content')
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <h3 class="mb-0 fw-bold text-primary">
            <i class="bi bi-journal-bookmark-fill me-3"></i>
            Sổ: {{ $group->ma_so }} - {{ $group->ten_so }}
        </h3>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('so-theo-doi.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cột trái: Thêm hồ sơ vào sổ -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                <div class="card-header bg-success text-white fw-bold fs-5 d-flex align-items-center py-2 px-4">
                    Thêm hồ sơ vào sổ
                </div>
                <div class="card-body p-2 d-flex flex-column">
                    <form method="POST" action="{{ route('so-theo-doi.batch-add', $group) }}"
                        class="flex-grow-1 d-flex flex-column">
                        @csrf
                        <div class="flex-grow-1 mb-4">
                            <select name="ho_so_ids[]" class="form-select" multiple required style="min-height: 350px;">
                                @foreach ($hoSosChuaThem as $hs)
                                    <option value="{{ $hs->id }}">
                                        {{ $hs->ma_ho_so }} - {{ $hs->ten_chu_ho_so ?? 'Không tên' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-end mt-auto">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-lg"></i> Thêm hồ sơ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cột phải: Hồ sơ trong sổ -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                <div
                    class="card-header bg-info text-white fw-bold fs-5 d-flex justify-content-between align-items-center py-2 px-4">
                    <div>
                         Hồ sơ trong sổ
                        <span class="badge bg-light text-dark ms-2">{{ $hoSosTrongSo->total() }}</span>
                    </div>
                </div>
                <div class="card-body p-0 d-flex flex-column">
                    <form method="POST" action="{{ route('so-theo-doi.batch-remove', $group) }}" id="batch-remove-form"
                        class="flex-grow-1 d-flex flex-column">
                        @csrf
                        <div class="table-responsive flex-grow-1 p-2">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50"><input type="checkbox" id="check-all"></th>
                                        <th>Mã HS</th>
                                        <th>Chủ hồ sơ</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($hoSosTrongSo as $hs)
                                        <tr>
                                            <td><input type="checkbox" name="ho_so_ids[]" value="{{ $hs->id }}"></td>
                                            <td class="fw-medium">{{ $hs->ma_ho_so }}</td>
                                            <td>{{ data_get($hs, 'chu_su_dung.ho_ten') ?? '-' }}</td>
                                            <td>{{ $hs->trang_thai }}</td>
                                            <td>
                                                <a href="{{ route('ho-so.show', $hs) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Xem
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5 fs-5">
                                                Sổ này chưa có hồ sơ nào
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light text-end">
                            <button type="submit" class="btn btn-danger btn-sm px-4"
                                onclick="return confirm('Xóa các hồ sơ đã chọn khỏi sổ?');">
                                <i class="bi bi-trash"></i> Xóa chọn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination (nếu cần) -->
    <div class="mt-4">
        {{ $hoSosTrongSo->links() }}
    </div>

    <script>
        // Check all checkbox
        document.getElementById('check-all').addEventListener('click', function() {
            document.querySelectorAll('input[name="ho_so_ids[]"]').forEach(el => el.checked = this.checked);
        });

        // Ngăn submit nếu không chọn gì
        document.getElementById('batch-remove-form').addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('input[name="ho_so_ids[]"]:checked');
            if (checked.length === 0) {
                alert('Vui lòng chọn ít nhất 1 hồ sơ để xóa!');
                e.preventDefault();
            }
        });
    </script>
@endsection
