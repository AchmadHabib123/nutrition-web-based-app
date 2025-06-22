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
        Schema::table('bahan_makanans', function (Blueprint $table) {
            // Kolom untuk nilai porsi standar (misal: 150 gram, 1 buah)
            $table->decimal('standard_portion_value', 8, 2)->nullable()->after('total_lemak');
            // Kolom untuk unit porsi standar (misal: 'g', 'btr', 'ml', 'porsi')
            $table->string('standard_portion_unit', 50)->nullable()->after('standard_portion_value');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_makanans', function (Blueprint $table) {
            $table->dropColumn('standard_portion_value');
            $table->dropColumn('standard_portion_unit');
        });
    }
};
