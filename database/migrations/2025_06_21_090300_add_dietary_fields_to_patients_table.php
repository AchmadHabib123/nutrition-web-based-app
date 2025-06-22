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
        Schema::table('patients', function (Blueprint $table) {
            // Kolom untuk kondisi klinis diet pasien (BAB 2)
            // String untuk fleksibilitas, atau enum jika pilihannya terbatas dan statis
            $table->string('kondisi_diet_klinis')->nullable()->after('tipe_pasien'); // Contoh posisi

            // Foreign key untuk standar diit utama (BAB 4)
            // Ini akan menunjuk ke tabel 'standar_diits' yang akan dibuat nanti
            $table->foreignId('standar_diit_id')->nullable()->constrained('standar_diits')->onDelete('set null'); // Contoh posisi
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['standar_diit_id']);
            $table->dropColumn('standar_diit_id');
            $table->dropColumn('kondisi_diet_klinis');
        });
    }
};
