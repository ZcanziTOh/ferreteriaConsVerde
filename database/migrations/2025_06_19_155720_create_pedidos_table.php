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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->integer('IDPed')->autoIncrement();
            $table->date('fechPed');
            $table->decimal('totalPed', 10, 2);
            $table->date('fechEntrPed')->nullable();
            $table->string('estadPed', 20);
            $table->integer('IDprov')->nullable();
            $table->integer('IDEmp')->nullable();
            $table->timestamps();

            $table->foreign('IDprov')->references('IDprov')->on('proveedores');
            $table->foreign('IDEmp')->references('IDEmp')->on('empleados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
