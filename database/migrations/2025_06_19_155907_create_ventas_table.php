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
        Schema::create('ventas', function (Blueprint $table) {
            $table->integer('IDVent')->autoIncrement();
            $table->dateTime('fechVent');
            $table->decimal('totalVent', 10, 2);
            $table->string('metPagVent', 50)->nullable();
            $table->string('codSunatVent', 20)->nullable();
            $table->integer('IDClieNat')->nullable();
            $table->integer('IDClieJuri')->nullable();
            $table->integer('IDUsu')->nullable();
            $table->timestamps();

            $table->foreign('IDClieNat')->references('IDClieNat')->on('cliente_natural');
            $table->foreign('IDClieJuri')->references('IDClieJuri')->on('cliente_juridica');
            $table->foreign('IDUsu')->references('IDUsu')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
