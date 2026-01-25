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
        Schema::create('ho_so_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ho_so_id')
                ->constrained('ho_sos')
                ->cascadeOnDelete();

            $table->string('ten_file');
            $table->string('duong_dan');
            $table->string('loai_file')->nullable();
            $table->unsignedBigInteger('kich_thuoc')->nullable();
            $table->string('ghi_chu')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_so_files');
    }
};
