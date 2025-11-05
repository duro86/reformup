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
        //Tabla Medios (poliformica)
        Schema::create('medios', function (Blueprint $table) {
            $table->id();
            $table->morphs('model'); // model_type, model_id + índice
            $table->string('ruta',255);
            $table->string('tipo',40);
            $table->string('titulo',160)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            //Indices para búsquedas rápidas
            $table->index('tipo');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Eliminar tabla medios
        Schema::dropIfExists('medios');
    }
};
