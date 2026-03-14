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
        Schema::create('siklus_menus', function (Blueprint $table) {
            $table->id();
            $table->integer('hari_siklus')->comment('Hari ke berapa dalam siklus (1-11)'); // Sesuai dokumen siklus menu
            $table->enum('waktu_makan', ['pagi', 'selingan_pagi', 'siang', 'selingan_siang', 'sore', 'selingan_sore'])->comment('Waktu makan'); // Sesuai dokumen siklus menu
            $table->enum('tipe_pasien', ['VVIP', 'VIP', 'Normal'])->comment('Tipe pasien yang dilayani'); // Jika siklus berbeda per tipe pasien

            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade'); // Menu yang disajikan

            // Tambahkan unique constraint untuk mencegah duplikasi entri siklus
            $table->unique(['hari_siklus', 'waktu_makan', 'tipe_pasien'], 'unique_siklus_slot');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siklus_menus');
    }
};
