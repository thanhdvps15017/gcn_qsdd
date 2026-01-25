<?php

namespace App\Exports;

use App\Models\SoTheoDoiGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SoTheoDoiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $group;

    public function __construct(SoTheoDoiGroup $group)
    {
        $this->group = $group;
    }

    public function collection()
    {
        return $this->group->hoSos()
            ->with(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Mã hồ sơ',
            'Xưng hô',
            'Tên chủ hồ sơ',
            'SDT chủ hồ sơ',
            'Loại hồ sơ',
            'Loại thủ tục',
            'Xã',
            'Người thẩm tra',

            // chu_su_dung
            'Chủ sử dụng - Họ tên',
            'Chủ sử dụng - CCCD',
            'Chủ sử dụng - Ngày cấp',
            'Chủ sử dụng - Địa chỉ',

            // uy_quyen
            'Ủy quyền - Người',
            'Ủy quyền - Giấy tờ',

            // thua_chung (chỉ 1 thửa đầu tiên)
            'Thửa chung 1 - Tờ',
            'Thửa chung 1 - Thửa',
            'Thửa chung 1 - Diện tích',

            // thong_tin_rieng
            'Thông tin riêng - Loại',
            'Thông tin riêng - Họ tên',
            'Thông tin riêng - CCCD',
            'Thông tin riêng - Ngày cấp CCCD',
            'Thông tin riêng - Địa chỉ',
            'Thông tin riêng - Thửa 1 - Tờ',
            'Thông tin riêng - Thửa 1 - Thửa',
            'Thông tin riêng - Thửa 1 - Diện tích',

            // Các trường còn lại
            'Ngày cấp GCN',
            'Số vào sổ',
            'Số phát hành',
            'Xã/Ấp/Thôn',
            'Ghi chú',
            'Hạn giải quyết',
            'Trạng thái',
            'Ngày tạo',
            'Ngày cập nhật',
        ];
    }

    public function map($hoSo): array
    {
        // Đảm bảo các trường JSON là mảng (dù cast hay chuỗi)
        $chuSuDung     = $this->toArray($hoSo->chu_su_dung);
        $uyQuyen       = $this->toArray($hoSo->uy_quyen);
        $thuaChung     = $this->toArray($hoSo->thua_chung);
        $thongTinRieng = $this->toArray($hoSo->thong_tin_rieng);
        $dataRieng     = $this->toArray($thongTinRieng['data'] ?? []);

        // Chỉ lấy thửa đầu tiên của thua_chung
        $thuaChungItem = $thuaChung[0] ?? [];
        $thuaChungTo   = $thuaChungItem['to']        ?? '';
        $thuaChungThua = $thuaChungItem['thua']      ?? '';
        $thuaChungDT   = $thuaChungItem['dien_tich'] ?? '';

        // Chỉ lấy thửa đầu tiên của thong_tin_rieng['data']['thua']
        $thuaRiengList = $dataRieng['thua'] ?? [];
        $thuaRiengItem = $thuaRiengList[0] ?? [];
        $thuaRiengTo   = $thuaRiengItem['to']        ?? '';
        $thuaRiengThua = $thuaRiengItem['thua']      ?? '';
        $thuaRiengDT   = $thuaRiengItem['dien_tich'] ?? '';

        return [
            $hoSo->ma_ho_so ?? '',
            $hoSo->xung_ho ?? '',
            $hoSo->ten_chu_ho_so ?? '',
            $hoSo->sdt_chu_ho_so ?? '',

            optional($hoSo->loaiHoSo)->name ?? '—',
            optional($hoSo->loaiThuTuc)->name ?? '—',
            optional($hoSo->xa)->name ?? '—',
            optional($hoSo->nguoiThamTra)->name ?? '—',

            // chu_su_dung
            $chuSuDung['ho_ten']   ?? '',
            $chuSuDung['cccd']     ?? '',
            $chuSuDung['ngay_cap'] ?? '',
            $chuSuDung['dia_chi']  ?? '',

            // uy_quyen
            $uyQuyen['nguoi'] ?? '',
            $uyQuyen['giay']  ?? '',

            // thua_chung - chỉ thửa 1
            $thuaChungTo,
            $thuaChungThua,
            $thuaChungDT,

            // thong_tin_rieng
            $thongTinRieng['loai']       ?? '',
            $dataRieng['ho_ten']         ?? '',
            $dataRieng['cccd']           ?? '',
            $dataRieng['ngay_cap_cccd']  ?? '',
            $dataRieng['dia_chi']        ?? '',
            $thuaRiengTo,
            $thuaRiengThua,
            $thuaRiengDT,

            // còn lại
            $hoSo->ngay_cap_gcn ? $hoSo->ngay_cap_gcn->format('d/m/Y') : '',
            $hoSo->so_vao_so ?? '',
            $hoSo->so_phat_hanh ?? '',
            $hoSo->xa_ap_thon ?? '',
            $hoSo->ghi_chu ?? '',
            $hoSo->han_giai_quyet ? $hoSo->han_giai_quyet->format('d/m/Y') : '',
            $hoSo->trang_thai_meta['text'] ?? ($hoSo->trang_thai ?? '—'),
            $hoSo->created_at?->format('d/m/Y H:i') ?? '',
            $hoSo->updated_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    /**
     * Helper: Chuyển đổi thành mảng an toàn
     */
    private function toArray($value): array
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}
