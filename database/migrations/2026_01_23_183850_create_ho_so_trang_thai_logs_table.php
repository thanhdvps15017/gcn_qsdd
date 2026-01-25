<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ho_so_trang_thai_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ho_so_id')
                ->constrained('ho_sos')
                ->cascadeOnDelete();

            $table->string('trang_thai_cu')->nullable();
            $table->string('trang_thai_moi');

            $table->foreignId('user_id')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_so_trang_thai_logs');
    }
};
