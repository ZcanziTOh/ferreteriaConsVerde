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
        Schema::create('detalle_pedido', function (Blueprint $table) {
            $table->integer('IDdetalle')->autoIncrement();
            $table->integer('cant');
            $table->decimal('precUni', 10, 2);
            $table->string('nomProd', 50);
            $table->integer('IDPed')->nullable();
            $table->integer('IDProd')->nullable();
            $table->timestamps();

            $table->foreign('IDPed')->references('IDPed')->on('pedidos');
            $table->foreign('IDProd')->references('IDProd')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pedido');
    }
};
