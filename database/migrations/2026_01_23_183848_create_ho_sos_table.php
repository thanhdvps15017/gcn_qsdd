<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ho_sos', function (Blueprint $table) {
            $table->id();

            // ===== THÔNG TIN CHUNG =====
            $table->string('ma_ho_so')->unique();
            $table->string('xung_ho')->nullable();
            $table->string('ten_chu_ho_so')->nullable();
            $table->string('sdt_chu_ho_so')->nullable();

            $table->foreignId('loai_ho_so_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loai_thu_tuc_id')->constrained()->cascadeOnDelete();
            $table->foreignId('xa_id')->constrained()->cascadeOnDelete();
            // cho cascadeOnDelete để khi user bị xoá các hồ sơ liên quan cũng bị xoá
            $table->foreignId('nguoi_tham_tra_id')->constrained('users')->cascadeOnDelete();

            // ===== JSON =====
            $table->json('chu_su_dung')->nullable();
            $table->json('uy_quyen')->nullable();
            $table->json('thua_chung')->nullable();

            // ===== GCN =====
            $table->date('ngay_cap_gcn')->nullable();
            $table->string('so_vao_so')->nullable();
            $table->string('so_phat_hanh')->nullable();
            $table->string('xa_ap_thon')->nullable();

            // ===== THÔNG TIN RIÊNG =====
            $table->json('thong_tin_rieng')->nullable();

            // ===== HẠN TRẢ KẾT QUẢ =====
            $table->date('han_giai_quyet')->nullable();

            // ===== GHI CHÚ =====
            $table->text('ghi_chu')->nullable();
            $table->string('trang_thai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ho_sos');
    }
};
