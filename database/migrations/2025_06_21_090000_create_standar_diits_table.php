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
        Schema::create('standar_diits', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // Nama standar diet, misal: "Standar Diet Umum", "Standar Diet Diabetes"

            // Parameter kuantitatif
            $table->decimal('min_kalori', 8, 2)->nullable();
            $table->decimal('max_kalori', 8, 2)->nullable();
            $table->decimal('target_protein_ratio', 5, 2)->nullable(); // Misal 0.20 untuk 20%
            $table->decimal('target_karbohidrat_ratio', 5, 2)->nullable();
            $table->decimal('target_lemak_ratio', 5, 2)->nullable();

            // Parameter kualitatif (disimpan sebagai JSON untuk fleksibilitas)
            $table->json('kategori_bahan_terlarang')->nullable(); // Misal: ["tinggi gula", "tinggi garam"]
            $table->json('bahan_makanan_terlarang_ids')->nullable(); // Misal: [1, 5, 12] (ID bahan makanan)

            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standar_diits');
    }
};
