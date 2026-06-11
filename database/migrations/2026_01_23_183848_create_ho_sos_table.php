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
            $table->string('dossier_code')->unique();
            $table->string('salutation')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('owner_phone')->nullable();

            $table->foreignId('dossier_type_id')->nullable()->constrained('loai_ho_sos')->nullOnDelete();
            $table->foreignId('procedure_type_id')->nullable()->constrained('loai_thu_tucs')->nullOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained('xas')->nullOnDelete();
            // nullOnDelete để đảm bảo an toàn dữ liệu, không xóa hồ sơ khi xóa danh mục
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();

            // ===== JSON =====
            $table->json('land_owners')->nullable();
            $table->json('authorization')->nullable();
            $table->json('shared_plots')->nullable();

            // ===== GCN =====
            $table->date('certificate_issue_date')->nullable();
            $table->string('registration_book_number')->nullable();
            $table->string('publication_number')->nullable();
            $table->string('address_details')->nullable();

            // ===== THÔNG TIN RIÊNG =====
            $table->json('private_info')->nullable();

            // ===== HẠN TRẢ KẾT QUẢ =====
            $table->date('deadline')->nullable();

            // ===== GHI CHÚ =====
            $table->text('notes')->nullable();
            $table->string('status')->nullable();

            $table->timestamps();

            // ===== INDEXES =====
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ho_sos');
    }
};
