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
        //Tabla Profesional - Oficio (relación muchos a muchos)
        Schema::create('profesional_oficio', function (Blueprint $table) {
            $table->foreignId('pro_id')->constrained('perfiles_profesionales')->onDelete('cascade');
            $table->foreignId('oficio_id')->constrained('oficios')->onDelete('restrict');
            $table->primary(['pro_id', 'oficio_id']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesional_oficio');
    }
};
