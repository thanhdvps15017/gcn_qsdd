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
    $uyQuyen = old('uy_quyen', $isEdit ? $hoSo->uy_quyen ?? [] : []);
    $thuaChung = old('thua_chung', $isEdit ? array_values((array) ($hoSo->thua_chung ?? [])) : []);
    $thongTinRieng = old('thong_tin_rieng', $isEdit ? $hoSo->thong_tin_rieng ?? [] : []);
    $riengLoai = $thongTinRieng['loai'] ?? '';
    $riengData = $thongTinRieng['data'] ?? [];
    $riengThua = array_values((array) ($riengData['thua'] ?? []));

    if (empty($thuaChung)) {
        $thuaChung = [['to' => '', 'thua' => '', 'dien_tich' => '']];
    }

    // Chuẩn bị index cho JS
    $chuSuDungList = old('chu_su_dung', []);
    if ($isEdit) {
        $chuSuDungList = is_array($hoSo->chu_su_dung)
            ? array_values($hoSo->chu_su_dung)
            : ($hoSo->chu_su_dung
                ? [$hoSo->chu_su_dung]
                : [[]]);
    }
    if (empty($chuSuDungList)) {
        $chuSuDungList = [[]];
    }
    $chuSuDungIndex = count($chuSuDungList);

    $nguoiLienQuan = old('thong_tin_rieng.data.nguoi_lien_quan', $riengData['nguoi_lien_quan'] ?? []);
    $nguoiIndex = count($nguoiLienQuan) ?: 1;
@endphp

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

    <input type="hidden" name="trang_thai"
        value="{{ old('trang_thai', $isEdit ? $hoSo->trang_thai : 'dang_giai_quyet') }}">

    <div class="text-end mt-5">
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

        document.getElementById('han_giai_quyet').value = currentDate.toISOString().split('T')[0];
    }

    document.addEventListener('DOMContentLoaded', () => {
        const loaiSelect = document.querySelector('select[name="loai_thu_tuc_id"]');
        if (loaiSelect && loaiSelect.selectedIndex > 0) {
            tinhHanTra(loaiSelect);
        }

        // Xóa file đã upload
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
                    .then(res => res.ok ? res.json() : Promise.reject())
                    .then(() => document.getElementById(`file-row-${fileId}`)?.remove())
                    .catch(() => alert('Không thể xóa file'));
            });
        });
    });
</script>
