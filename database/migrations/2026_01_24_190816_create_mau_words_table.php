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
        Schema::create('mau_words', function (Blueprint $table) {
            $table->id();
            $table->string('ten');
            $table->string('file_path');

            $table->text('ghi_chu')
                ->nullable();

            $table->string('file_dinh_kem', 255)
                ->nullable();

            $table->foreignId('folder_id')
                ->nullable()
                ->constrained('mau_word_folders')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mau_words');
    }
};
