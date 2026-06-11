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
            $table->string('book_name')->unique();
            $table->string('book_code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('so_theo_doi_groups');
    }
};
