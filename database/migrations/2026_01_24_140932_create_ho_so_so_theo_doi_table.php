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
            $table->foreignId('tracking_book_id')->constrained('so_theo_doi_groups')->cascadeOnDelete();
            $table->string('notes')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_so_so_theo_doi');
    }
};
