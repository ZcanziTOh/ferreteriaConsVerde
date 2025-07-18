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
        Schema::create('productos', function (Blueprint $table) {
            $table->integer('IDProd')->autoIncrement();
            $table->string('nomProd', 50);
            $table->string('estProd', 11);
            $table->string('uniMedProd', 20);
            $table->decimal('precUniProd', 10, 2);
            $table->integer('stockProd')->default(0);
            $table->integer('stockMinProd')->default(0);
            $table->integer('IDCat')->nullable();
            $table->integer('IDprov')->nullable();
            $table->timestamps();

            $table->foreign('IDCat')->references('IDCat')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
