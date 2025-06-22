<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standar_diits', function (Blueprint $table) {
            // Kolom untuk menyimpan pola porsi kategori dalam format JSON
            // Contoh struktur JSON:
            // {
            //   "pagi": {
            //     "makanan_pokok": {"usia_1_3_tahun": 75, "usia_4_6_tahun": 100},
            //     "lauk_hewani": {"usia_1_3_tahun": 25},
            //     "sayur": {"usia_1_3_tahun": 50},
            //     "susu": {"usia_1_3_tahun": 150}
            //   },
            //   "siang": { ... },
            //   "sore": { ... },
            //   "selingan_pagi": { "roti": {"umum": 1} },
            //   "selingan_siang": { "buah": {"umum": 1} }
            // }
            $table->json('pola_porsi_kategori')->nullable()->after('bahan_makanan_terlarang_ids');
        });
    }

    public function down(): void
    {
        Schema::table('standar_diits', function (Blueprint $table) {
            $table->dropColumn('pola_porsi_kategori');
        });
    }
};