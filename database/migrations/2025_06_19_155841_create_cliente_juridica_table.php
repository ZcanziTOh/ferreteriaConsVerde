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
        Schema::create('cliente_juridica', function (Blueprint $table) {
            $table->integer('IDClieJuri')->autoIncrement();
            $table->string('razSociClieJuri', 150);
            $table->string('dirfiscClieJuri', 150);
            $table->char('rucClieJuri', 11);
            $table->string('nomComClieJuri', 100)->nullable();
            $table->string('perRespClieJuri', 100)->nullable();
            $table->string('rubrClieJuri', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_juridica');
    }
};
