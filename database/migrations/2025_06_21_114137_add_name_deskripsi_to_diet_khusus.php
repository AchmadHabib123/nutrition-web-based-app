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
        Schema::table('diet_khusus', function (Blueprint $table) {
            $table->string('nama')->unique(); // Nama diet khusus, misal: "Diet Diabetes"
            $table->text('deskripsi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diet_khusus', function (Blueprint $table) {
            //
        });
    }
};
