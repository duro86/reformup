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
        
        //Tabla Solicitudes
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pro_id')->nullable()->constrained('perfiles_profesionales')->nullOnDelete();
            $table->foreignId('cliente_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo', 160);
            $table->text('descripcion');
            $table->string('ciudad', 120)->nullable();
            $table->string('provincia', 120)->nullable();
            $table->string('dir_empresa', 255)->nullable();
            $table->enum('estado',['abierta','en_revision','cerrada','cancelada'])->default('abierta')->index();
            $table->decimal('presupuesto_max', 10, 2)->nullable();
            $table->dateTime('fecha')->useCurrent();;
            $table->timestamps();
            $table->softDeletes();

            //Indices para búsquedas rápidas
            $table->index('cliente_id');
            $table->index('pro_id');
            $table->index(['ciudad', 'provincia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         //Eliminar tabla solicitudes
        Schema::dropIfExists('solicitudes');
    }
};
