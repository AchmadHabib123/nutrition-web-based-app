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
        Schema::table('food_consumptions', function (Blueprint $table) {
            $table->integer('kalori')->nullable()->after('nama_makanan');
            $table->string('waktu_makan')->after('nama_makanan')->nullable();
            $table->string('status')->default('planned')->after('kalori');
            $table->date('tanggal')->nullable()->after('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_consumptions', function (Blueprint $table) {
            $table->dropColumn('kalori');
            $table->dropColumn('waktu_makan');
            $table->dropColumn('status');
            $table->dropColumn('tanggal');
        });
    }
};
