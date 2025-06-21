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
        Schema::create('empleados', function (Blueprint $table) {
            $table->integer('IDEmp')->autoIncrement();
            $table->string('nomEmp', 30);
            $table->string('apelEmp', 30);
            $table->string('docIdenEmp', 20);
            $table->string('telEmp', 20)->nullable();
            $table->string('dirEmp', 80);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
