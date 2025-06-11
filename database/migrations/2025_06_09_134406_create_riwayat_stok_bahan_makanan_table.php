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
        Schema::create('riwayat_stok_bahan_makanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_makanan_id')->constrained('bahan_makanans')->onDelete('cascade');
            $table->enum('tipe', ['masuk', 'keluar']); // Tipe transaksi: stok masuk atau keluar
            $table->integer('jumlah'); // Jumlah yang masuk atau keluar
            $table->string('satuan'); // Contoh: gram, kg, liter
            $table->text('keterangan')->nullable(); // Opsional: catatan tambahan
            $table->timestamps(); // Akan otomatis membuat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_stok_bahan_makanan');
    }
};
