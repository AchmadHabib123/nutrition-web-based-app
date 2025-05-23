<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama makanan jadi
            $table->text('deskripsi')->nullable(); // Opsional
            $table->enum('tipe_pasien', ['VVIP', 'VIP', 'Normal']);
            $table->string('gambar')->nullable();

            // Total nutrisi (bisa diisi otomatis berdasarkan bahan)
            $table->integer('total_protein')->default(0);
            $table->integer('total_karbohidrat')->default(0);
            $table->integer('total_lemak')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
}