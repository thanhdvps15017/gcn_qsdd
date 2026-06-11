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

            // land_owners
            'Chủ sử dụng - Họ tên',
            'Chủ sử dụng - CCCD',
            'Chủ sử dụng - Ngày cấp',
            'Chủ sử dụng - Địa chỉ',

            // authorization
            'Ủy quyền - Người',
            'Ủy quyền - Giấy tờ',

            // shared_plots (chỉ 1 thửa đầu tiên)
            'Thửa chung 1 - Tờ',
            'Thửa chung 1 - Thửa',
            'Thửa chung 1 - Diện tích',

            // private_info
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
