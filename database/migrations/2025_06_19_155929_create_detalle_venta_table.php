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
        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->integer('IDDetall_vent')->autoIncrement();
            $table->decimal('prec_uni', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->integer('IDProd')->nullable();
            $table->integer('IDVent')->nullable();
            $table->timestamps();

            $table->foreign('IDProd')->references('IDProd')->on('productos');
            $table->foreign('IDVent')->references('IDVent')->on('ventas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
    }
};
