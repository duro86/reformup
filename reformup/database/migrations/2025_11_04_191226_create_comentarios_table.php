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
        //Tabla Comentarios
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajo_id')->constrained('trabajos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('users')->restrictOnDelete();
            $table->tinyInteger('puntuacion')->unsigned(); // 1..5
            $table->enum('estado',['pendiente','publicado','rechazado'])->default('pendiente')->index();
            $table->text('opinion')->nullable();
            $table->boolean('visible')->default(false);
            $table->dateTime('fecha')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['trabajo_id', 'cliente_id']);

            //Indices para búsquedas rápidas
            $table->index('visible');
            $table->index('puntuacion');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Eliminar tabla comentarios
        Schema::dropIfExists('comentarios');
    }
};
