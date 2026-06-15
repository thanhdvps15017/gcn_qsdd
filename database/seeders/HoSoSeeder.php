<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HoSo;
use App\Models\LoaiHoSo;
use App\Models\LoaiThuTuc;
use App\Models\Xa;
use App\Models\User;
use Carbon\Carbon;

class HoSoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loaiHoSos = LoaiHoSo::all();
        $loaiThuTucs = LoaiThuTuc::all();
        $xas = Xa::all();
        $inspector = User::first(); // Thường là superadmin ID = 1

        if ($loaiHoSos->isEmpty() || $loaiThuTucs->isEmpty() || $xas->isEmpty() || !$inspector) {
            $this->command->warn('Vui lòng chạy MasterDataSeeder và DatabaseSeeder trước để tạo dữ liệu nền!');
            return;
        }

        $vietnameseFirstNames = ['Anh', 'Bình', 'Chi', 'Dũng', 'Đông', 'Giang', 'Hải', 'Hùng', 'Hương', 'Khánh', 'Lâm', 'Minh', 'Nam', 'Nga', 'Phong', 'Quỳnh', 'Sơn', 'Thảo', 'Trang', 'Tuấn', 'Vinh', 'Xuân', 'Yến'];
        $vietnameseMiddleNames = ['Văn', 'Thị', 'Đức', 'Hữu', 'Ngọc', 'Minh', 'Thành', 'Thu', 'Kim', 'Bảo', 'Hoàng', 'Quốc'];
        $vietnameseLastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý'];

        $generateName = function () use ($vietnameseFirstNames, $vietnameseMiddleNames, $vietnameseLastNames) {
            $last = $vietnameseLastNames[array_rand($vietnameseLastNames)];
            $middle = $vietnameseMiddleNames[array_rand($vietnameseMiddleNames)];
            $first = $vietnameseFirstNames[array_rand($vietnameseFirstNames)];
            return "$last $middle $first";
        };

        $generateCCCD = function () {
            return '0' . rand(30, 99) . '0' . rand(10000000, 99999999);
        };

        $generatePhone = function () {
            $prefixes = ['090', '091', '098', '097', '096', '093', '034', '035', '038'];
            return $prefixes[array_rand($prefixes)] . rand(1000000, 9999999);
        };

        $statuses = [
            'dang_giai_quyet',
            'cho_bo_sung',
            'khong_du_dieu_kien',
            'chuyen_thue',
            'hs_niem_yet_xa',
            'phoi_hop_do_dac',
            'co_phieu_bao',
            'in_gcn_qsdd',
            'hoan_thanh',
        ];

        $dossierCount = 15;

        // Tìm hậu tố số lớn nhất của mã hồ sơ dạng HS-XXXX/YY hiện có để tránh trùng lặp khi chạy seeder nhiều lần
        $maxNum = 0;
        $existingCodes = HoSo::pluck('dossier_code')->toArray();
        foreach ($existingCodes as $code) {
            if (preg_match('/HS-(\d+)\/\d+/', $code, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $start = $maxNum + 1;
        $end = $start + $dossierCount - 1;

        for ($i = $start; $i <= $end; $i++) {
            $dossierCode = sprintf('HS-%04d/%02d', $i, date('y'));
            $ownerName = $generateName();
            $ownerPhone = $generatePhone();
            
            $loaiHoSo = $loaiHoSos->random();
            $loaiThuTuc = $loaiThuTucs->random();
            $xa = $xas->random();

            $receiveDate = Carbon::now()->subDays(rand(1, 30));
            $daysToProcess = $loaiThuTuc->processing_days ?? 15;
            $deadline = (clone $receiveDate)->addDays($daysToProcess);

            // Tạo danh sách chủ sử dụng đất
            $owners = [];
            $salutations = ['Ông', 'Bà'];
            $ownerCount = rand(1, 2);
            for ($o = 0; $o < $ownerCount; $o++) {
                $owners[] = [
                    'salutation' => $salutations[array_rand($salutations)],
                    'full_name' => $o === 0 ? $ownerName : $generateName(),
                    'date_of_birth' => Carbon::now()->subYears(rand(20, 65))->subDays(rand(1, 365))->format('Y-m-d'),
                    'id_card' => $generateCCCD(),
                    'issue_date' => Carbon::now()->subYears(rand(1, 10))->format('Y-m-d'),
                    'address' => 'Số ' . rand(1, 100) . ' Đường ' . rand(1, 20) . ', ấp ' . rand(1, 5) . ', xã ' . $xa->name . ', tỉnh Bình Phước',
                ];
            }

            // Thửa đất trước biến động
            $plots = [];
            $plotCount = rand(1, 3);
            for ($p = 0; $p < $plotCount; $p++) {
                $plots[] = [
                    'map_sheet' => rand(1, 150),
                    'plot_number' => rand(1, 800),
                    'area' => rand(1000, 10000) / 10,
                    'ward_id' => $xa->id,
                    'hamlet' => 'Ấp ' . rand(1, 5),
                ];
            }

            // Thông tin sau biến động (nếu có)
            $hasBienDong = (rand(0, 1) === 1);
            $privateInfo = null;
            if ($hasBienDong) {
                $bdTypes = ['chuyen_nhuong', 'tang_cho', 'thua_ke', 'tach_thua'];
                $bdType = $bdTypes[array_rand($bdTypes)];
                
                $related = [];
                $relCount = rand(1, 2);
                for ($r = 0; $r < $relCount; $r++) {
                    $related[] = [
                        'salutation' => $salutations[array_rand($salutations)],
                        'full_name' => $generateName(),
                        'date_of_birth' => Carbon::now()->subYears(rand(20, 60))->format('Y-m-d'),
                        'id_card' => $generateCCCD(),
                        'id_issue_date' => Carbon::now()->subYears(rand(1, 8))->format('Y-m-d'),
                        'address' => 'Ấp ' . rand(1, 5) . ', xã ' . $xa->name,
                    ];
                }

                $newPlots = [];
                $newPlotCount = rand(1, 2);
                for ($np = 0; $np < $newPlotCount; $np++) {
                    $newPlots[] = [
                        'map_sheet' => rand(1, 150),
                        'plot_number' => rand(1, 800),
                        'area' => rand(500, 5000) / 10,
                        'notes' => 'Tách thửa từ thửa cũ',
                    ];
                }

                $privateInfo = [
                    'type' => $bdType,
                    'data' => [
                        'related_person' => $related,
                        'plot_number' => $newPlots,
                    ]
                ];
            }

            // Ủy quyền
            $hasAuthorization = (rand(0, 2) === 1);
            $authorization = null;
            if ($hasAuthorization) {
                $authorization = [
                    'person' => $generateName(),
                    'paper' => 'Hợp đồng ủy quyền số ' . rand(100, 999) . '/UQ-CC'
                ];
            }

            HoSo::create([
                'dossier_code' => $dossierCode,
                'owner_name' => $ownerName,
                'owner_phone' => $ownerPhone,
                'dossier_type_id' => $loaiHoSo->id,
                'procedure_type_id' => $loaiThuTuc->id,
                'inspector_id' => $inspector->id,
                'ward_id' => $xa->id,
                'notes' => 'Hồ sơ thử nghiệm tạo tự động bởi seeder.',
                'shared_plots' => $plots,
                'private_info' => $privateInfo,
                'land_owners' => $owners,
                'authorization' => $authorization,
                'deadline' => $deadline->format('Y-m-d'),
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $receiveDate,
                'updated_at' => $receiveDate,
            ]);
        }

        $this->command->info("Đã tạo thành công $dossierCount hồ sơ mẫu!");
    }
}
