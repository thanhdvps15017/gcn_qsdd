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
        Schema::create('ho_so_so_theo_doi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ho_so_id')->constrained('ho_sos')->cascadeOnDelete();
            $table->foreignId('so_theo_doi_group_id')->constrained('so_theo_doi_groups')->cascadeOnDelete();
            $table->string('ghi_chu')->nullable();
            $table->string('thu_tu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_so_so_theo_doi_tables');
    }
};
