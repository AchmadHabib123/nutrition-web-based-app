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
        Schema::table('patients', function (Blueprint $table) {
            $table->float('berat_badan')->nullable(); // dalam kg
            $table->float('tinggi_badan')->nullable(); // dalam cm
            $table->integer('usia')->nullable(); // dalam tahun
            $table->enum('jenis_kelamin', ['pria', 'wanita'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['berat_badan', 'tinggi_badan', 'usia', 'jenis_kelamin']);
        });
    }
};
