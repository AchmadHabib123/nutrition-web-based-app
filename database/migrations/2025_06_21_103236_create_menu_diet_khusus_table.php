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
        Schema::create('menu_diet_khusus', function (Blueprint $table) {
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('diet_khusus_id')->constrained('diet_khusus')->onDelete('cascade');
            $table->primary(['menu_id', 'diet_khusus_id']); // Compound primary key
            $table->timestamps(); // Opsional, tapi baik untuk audit
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_diet_khusus');
    }
};
