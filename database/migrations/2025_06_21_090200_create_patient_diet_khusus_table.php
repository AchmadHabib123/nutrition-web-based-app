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
        Schema::create('patient_diet_khusus', function (Blueprint $table) {
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('diet_khusus_id')->constrained('diet_khusus')->onDelete('cascade');
            $table->primary(['patient_id', 'diet_khusus_id']); // Compound primary key
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_diet_khusus');
    }
};
