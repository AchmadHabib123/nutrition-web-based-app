<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type; // Diperlukan jika menggunakan Doctrine DBAL

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan Doctrine DBAL terinstal jika Anda belum
        // composer require doctrine/dbal

        Schema::table('standar_diits', function (Blueprint $table) {
            // Ubah kolom dari longtext menjadi json
            // Pastikan nama kolom sama persis seperti yang ada
            $table->json('pola_porsi_kategori')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('standar_diits', function (Blueprint $table) {
            // Jika ingin rollback, ubah kembali ke longtext
            $table->longText('pola_porsi_kategori')->nullable()->change();
        });
    }
};