<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBahanMenuTable extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_menu', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('bahan_makanan_id')->constrained('bahan_makanans')->onDelete('cascade');

            $table->integer('jumlah'); // dalam gram
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_menu');
    }
}
