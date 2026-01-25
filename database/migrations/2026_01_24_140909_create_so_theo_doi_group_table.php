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
        Schema::create('so_theo_doi_groups', function (Blueprint $table) {
            $table->id();
            $table->string('ten_so')->unique();
            $table->string('ma_so')->unique()->nullable();
            $table->text('mo_ta')->nullable();
            $table->foreignId('nguoi_tao_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('so_theo_doi_group_tables');
    }
};
