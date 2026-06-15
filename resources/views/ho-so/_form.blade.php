@php
    $title ??= 'THÊM MỚI HỒ SƠ';
    $action ??= route('ho-so.store');
    $method ??= 'POST';
    $submitText ??= 'Lưu hồ sơ';
    $hoSo ??= null;
    $isEdit = !is_null($hoSo);

    // Helper
    $getValue = fn($key, $default = '') => old($key, $isEdit ? data_get($hoSo, $key, $default) : $default);

    // Dữ liệu động
    $uyQuyen = old('authorization', $isEdit ? $hoSo->authorization ?? [] : []);
    $thuaChung = old('shared_plots', $isEdit ? array_values((array) ($hoSo->shared_plots ?? [])) : []);
    
    $thongTinRieng = old('private_info', $isEdit ? $hoSo->private_info ?? [] : []);
    $riengLoai = $thongTinRieng['type'] ?? '';
    $riengData = $thongTinRieng['data'] ?? [];
    $riengThua = array_values((array) ($riengData['plot_number'] ?? $riengData['thua'] ?? []));

    if (empty($thuaChung)) {
        $thuaChung = [['map_sheet' => '', 'plot_number' => '', 'area' => '']];
    }

    // Chuẩn bị index cho JS
    $chuSuDungList = old('land_owners', $isEdit ? ($hoSo->land_owners ?? []) : []);
    if (!is_array($chuSuDungList)) {
        $chuSuDungList = [$chuSuDungList];
    }
    // Lấy lại các giá trị dưới dạng mảng index 0, 1, 2...
    $chuSuDungList = array_values($chuSuDungList);
    
    if (empty($chuSuDungList)) {
        $chuSuDungList = [[]];
    }
    $chuSuDungIndex = count($chuSuDungList);

    $nguoiLienQuan = array_values((array) ($riengData['related_person'] ?? []));
    $nguoiIndex = count($nguoiLienQuan) ?: 1;
@endphp

<style>
    /* Fix bootstrap-select inside input-group */
    .input-group > .bootstrap-select {
        width: 100px !important;
        flex: 0 0 100px !important;
    }
    .input-group > .bootstrap-select .dropdown-toggle {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .input-group > .bootstrap-select + .form-control {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
</style>

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <!-- Header -->
    <div class="card-header text-white d-flex justify-content-between align-items-center mb-4 rounded-1"
        style="background: linear-gradient(135deg, var(--primary), #0d6efd);">
        <h5 class="mb-0 fw-bold">{{ $title }}</h5>
        <a href="{{ route('ho-so.index') }}" class="btn btn-light btn-md d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Phần 1: Thông tin chung + Ghi chú & Tài liệu -->
    @include(
        'ho-so.partials._thong-tin-chung',
        compact('isEdit', 'hoSo', 'loaiHoSos', 'loaiThuTucs', 'xas', 'users'))

    <!-- Phần 2: Chủ sử dụng + Thửa đất -->
    @include(
        'ho-so.partials._chu-su-dung-va-thua',
        compact('isEdit', 'hoSo', 'xas', 'chuSuDungList', 'chuSuDungIndex', 'thuaChung', 'uyQuyen'))

    <!-- Phần 3: Thông tin sau biến động -->
    @include(
        'ho-so.partials._thong-tin-sau-bien-dong',
        compact('isEdit', 'hoSo', 'riengLoai', 'nguoiLienQuan', 'nguoiIndex', 'riengThua'))

    <input type="hidden" name="status"
        value="{{ old('status', $isEdit ? $hoSo->status : 'dang_giai_quyet') }}">

    <div class="text-end">
        <button type="submit" class="btn btn-success px-5 py-3 fw-bold">{{ $submitText }}</button>
    </div>
</form>

<!-- JS chung (chỉ những hàm dùng ở nhiều nơi) -->
<script>
    function tinhHanTra(select) {
        const days = select.options[select.selectedIndex]?.dataset.days;
        if (!days) return;

        let soNgayCanCong = parseInt(days, 10);
        let currentDate = new Date();
        let soNgayDaCong = 0;

        while (soNgayDaCong < soNgayCanCong) {
            currentDate.setDate(currentDate.getDate() + 1);
            if (currentDate.getDay() !== 0 && currentDate.getDay() !== 6) soNgayDaCong++;
        }

        document.getElementById('deadline').value = currentDate.toISOString().split('T')[0];
    }

    document.addEventListener('DOMContentLoaded', () => {
        const loaiSelect = document.querySelector('select[name="procedure_type_id"]');
        const hanGiaiQuyetInput = document.getElementById('deadline');
        if (loaiSelect && hanGiaiQuyetInput && !hanGiaiQuyetInput.value) {
            tinhHanTra(loaiSelect);
        }

        // Xóa file đã upload
        document.querySelectorAll('.btn-delete-file').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.dataset.url;
                const fileId = this.dataset.id;

                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: "Bạn có chắc chắn muốn xóa tài liệu này?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Có, xóa!',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(url, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')?.content || '',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.ok ? res.json() : Promise.reject())
                            .then(() => {
                                document.getElementById(`file-row-${fileId}`)?.remove();
                                if (typeof showToast === 'function') {
                                    showToast('Đã xóa tài liệu thành công!');
                                } else {
                                    Swal.fire('Thành công', 'Đã xóa tài liệu thành công!', 'success');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Lỗi', 'Không thể xóa file', 'error');
                            });
                    }
                });
            });
        });
    });
</script>
