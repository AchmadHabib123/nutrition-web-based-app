<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('no_kamar')->unique();
            $table->string('nama_pasien');
            $table->text('riwayat_penyakit');
            $table->integer('kalori_makanan')->default(0);
            $table->integer('kalori_harian')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
