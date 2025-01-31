<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama makanan
            $table->string('gambar')->nullable(); // Gambar makanan
            $table->integer('protein'); // Protein dalam gram
            $table->integer('karbohidrat'); // Karbohidrat dalam gram
            $table->integer('total_lemak'); // Total lemak dalam gram
            $table->enum('tipe_pasien', ['VVIP', 'VIP', 'Normal']); // Tipe pasien
            $table->enum('kategori_bahan_masakan', ['makanan_pokok', 'lauk', 'sayur', 'buah', 'susu']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
}
