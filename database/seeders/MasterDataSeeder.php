<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Xa;
use App\Models\LoaiHoSo;
use App\Models\LoaiThuTuc;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Xã / Phường
        $xas = [
            'Lộc Ninh',
            'Lộc Thạnh',
            'Lộc Thành',
            'Lộc Tấn',
            'Lộc Quang',
            'Lộc Hưng',
        ];

        foreach ($xas as $xa) {
            Xa::firstOrCreate(['name' => $xa]);
        }

        // 2. Seed Loại Hồ Sơ
        $loaiHoSos = [
            'Cấp Lại Bản Vẽ',
            'Đỗi Tên Bản Vẽ',
            'Chuyển mục đích',
            'Cấp mới',
            'Cấp Lại GCN',
            'Tách Thửa',
            'Chuyển quyền',
            'Cấp Đổi',
        ];

        foreach ($loaiHoSos as $loaiHoSo) {
            LoaiHoSo::firstOrCreate(['name' => $loaiHoSo]);
        }

        // 3. Seed Loại Thủ Tục
        // Loại 1 có 5 ngày xử lý, loại 2 bạn không để ngày nên mình set mặc định là null
        $loaiThuTucs = [
            ['name' => 'Trích lục bản đồ địa chính', 'processing_days' => 5],
            ['name' => 'Trích đo địa chính thửa đất', 'processing_days' => null],
        ];

        foreach ($loaiThuTucs as $loaiThuTuc) {
            LoaiThuTuc::firstOrCreate(
                ['name' => $loaiThuTuc['name']],
                ['processing_days' => $loaiThuTuc['processing_days']]
            );
        }
    }
}
