<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
};

class HoSoExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * Dữ liệu xuất ra
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * Header Excel
     */
    public function headings(): array
    {
        return [
            'STT',
            'Mã hồ sơ',
            'Tên chủ hồ sơ',
            'SĐT',
            'Loại hồ sơ',
            'Loại thủ tục',
            'Xã',
            'Người thẩm tra',
            'Trạng thái',
            'Hạn giải quyết',
            'Ngày tạo',
        ];
    }

    /**
     * Map từng dòng
     */
    public function map($hoSo): array
    {
        static $stt = 1;

        return [
            $stt++,
            $hoSo->ma_ho_so,
            $hoSo->ten_chu_ho_so,
            $hoSo->sdt_chu_ho_so,
            optional($hoSo->loaiHoSo)->ten,
            optional($hoSo->loaiThuTuc)->ten,
            optional($hoSo->xa)->ten,
            optional($hoSo->nguoiThamTra)->name,
            $hoSo->trangThaiMeta['text'] ?? '',
            optional($hoSo->han_giai_quyet)?->format('d/m/Y'),
            $hoSo->created_at?->format('d/m/Y'),
        ];
    }
}
