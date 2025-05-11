<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jadwal_makanan_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_makanan_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->date('tanggal'); // tanggal dari range
            $table->enum('waktu_makan', ['pagi', 'siang', 'malam']);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_makanan_menu');
    }
};
