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
        //Tabla Trabajos
        Schema::create('trabajos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presu_id')->unique()->constrained('presupuestos')->cascadeOnDelete();
            $table->date('fecha_ini')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado',['previsto','en_curso','finalizado','cancelado'])->default('previsto')->index();
            $table->string('dir_obra', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            //Indices para búsquedas rápidas
            $table->index('fecha_ini');
            $table->index('fecha_fin');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Eliminar tabla trabajos
        Schema::dropIfExists('trabajos');
    }
};
