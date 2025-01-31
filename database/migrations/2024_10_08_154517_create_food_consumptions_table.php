<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodConsumptionsTable extends Migration
{
    public function up()
    {
        Schema::create('food_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('nama_makanan');
            $table->integer('kalori');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('food_consumptions');
    }
}
