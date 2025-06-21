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
        Schema::create('cliente_natural', function (Blueprint $table) {
            $table->integer('IDClieNat')->autoIncrement();
            $table->string('docIdenClieNat', 20);
            $table->string('nomClieNat', 30);
            $table->string('apelClieNat', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_natural');
    }
};
