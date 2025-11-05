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
        //Tabla Presupuestos
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pro_id')->constrained('perfiles_profesionales')->cascadeOnDelete();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->decimal('total', 10, 2);
            $table->text('notas')->nullable();
            $table->enum('estado',['enviado','aceptado','rechazado','caducado'])->default('enviado')->index();
            $table->string('docu_pdf', 255)->nullable();
            $table->dateTime('fecha')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            //Indices para búsquedas rápidas
            $table->index('solicitud_id');
            $table->index('pro_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Eliminar tabla presupuestos
        Schema::dropIfExists('presupuestos');
    }
};
