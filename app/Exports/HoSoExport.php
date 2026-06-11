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

    public function collection()
    {
        return $this->data;
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
            'Chủ sử dụng - Họ tên',
            'Chủ sử dụng - CCCD',
            'Chủ sử dụng - Ngày cấp',
            'Chủ sử dụng - Địa chỉ',
            'Ủy quyền - Người',
            'Ủy quyền - Giấy tờ',
            'Thửa chung 1 - Tờ',
            'Thửa chung 1 - Thửa',
            'Thửa chung 1 - Diện tích',
            'Thông tin riêng - Loại',
            'Thông tin riêng - Họ tên',
            'Thông tin riêng - CCCD',
            'Thông tin riêng - Ngày cấp CCCD',
            'Thông tin riêng - Địa chỉ',
            'Thông tin riêng - Thửa 1 - Tờ',
            'Thông tin riêng - Thửa 1 - Thửa',
            'Thông tin riêng - Thửa 1 - Diện tích',
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
        $chuSuDung     = $this->toArray($hoSo->land_owners);
        $chuSuDungFirst = $chuSuDung[0] ?? [];
        $uyQuyen       = $this->toArray($hoSo->authorization);
        $thuaChung     = $this->toArray($hoSo->shared_plots);
        $thongTinRieng = $this->toArray($hoSo->private_info);
        $dataRieng     = $this->toArray($thongTinRieng['data'] ?? []);

        // Chỉ lấy thửa đầu tiên của shared_plots
        $thuaChungItem = $thuaChung[0] ?? [];
        $thuaChungTo   = $thuaChungItem['map_sheet']        ?? '';
        $thuaChungThua = $thuaChungItem['plot_number']      ?? '';
        $thuaChungDT   = $thuaChungItem['area'] ?? '';

        // Chỉ lấy thửa đầu tiên của private_info['data']['plot_number']
        $thuaRiengList = $dataRieng['plot_number'] ?? [];
        $thuaRiengItem = $thuaRiengList[0] ?? [];
        $thuaRiengTo   = $thuaRiengItem['map_sheet']        ?? '';
        $thuaRiengThua = $thuaRiengItem['plot_number']      ?? '';
        $thuaRiengDT   = $thuaRiengItem['area'] ?? '';

        return [
            $hoSo->dossier_code ?? '',
            $hoSo->salutation ?? '',
            $hoSo->owner_name ?? '',
            $hoSo->owner_phone ?? '',

            optional($hoSo->loaiHoSo)->name ?? '—',
            optional($hoSo->loaiThuTuc)->name ?? '—',
            optional($hoSo->xa)->name ?? '—',
            optional($hoSo->nguoiThamTra)->name ?? '—',

            // land_owners
            $chuSuDungFirst['full_name']   ?? '',
            $chuSuDungFirst['id_card']     ?? '',
            $chuSuDungFirst['issue_date'] ?? '',
            $chuSuDungFirst['address']  ?? '',

            // authorization
            $uyQuyen['person'] ?? '',
            $uyQuyen['paper']  ?? '',

            // shared_plots - chỉ thửa 1
            $thuaChungTo,
            $thuaChungThua,
            $thuaChungDT,

            // private_info
            $thongTinRieng['type']       ?? '',
            $dataRieng['full_name']         ?? '',
            $dataRieng['id_card']           ?? '',
            $dataRieng['id_issue_date']  ?? '',
            $dataRieng['address']        ?? '',
            $thuaRiengTo,
            $thuaRiengThua,
            $thuaRiengDT,

            // còn lại
            $hoSo->certificate_issue_date ? $hoSo->certificate_issue_date->format('d/m/Y') : '',
            $hoSo->registration_book_number ?? '',
            $hoSo->publication_number ?? '',
            $hoSo->address_details ?? '',
            $hoSo->notes ?? '',
            $hoSo->deadline ? $hoSo->deadline->format('d/m/Y') : '',
            $hoSo->trang_thai_meta['text'] ?? ($hoSo->status ?? '—'),
            $hoSo->created_at?->format('d/m/Y H:i') ?? '',
            $hoSo->updated_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    private function toArray($value): array
    {
        if (is_null($value)) return [];

        if (is_array($value)) return $value;

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded ?? [];
            }
        }

        return [];
    }
}
