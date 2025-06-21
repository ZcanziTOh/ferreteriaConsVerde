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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('IDUsu')->autoIncrement();
            $table->string('usuario', 50)->unique();
            $table->string('contraUsu', 255);
            $table->enum('rolUsu', ['admin', 'vendedor']);
            $table->integer('IDEmp')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();

            $table->foreign('IDEmp')->references('IDEmp')->on('empleados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
