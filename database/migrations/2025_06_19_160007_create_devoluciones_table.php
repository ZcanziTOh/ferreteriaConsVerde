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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->integer('IDDev')->autoIncrement();
            $table->dateTime('fechDev');
            $table->text('motivDev');
            $table->decimal('totalRembDev', 12, 2);
            $table->integer('IDUsu')->nullable();
            $table->integer('IDVent')->nullable();
            $table->timestamps();

            $table->foreign('IDUsu')->references('IDUsu')->on('users');
            $table->foreign('IDVent')->references('IDVent')->on('ventas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
