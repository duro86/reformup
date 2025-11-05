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
        //Tabla Oficios
        Schema::create('oficios', function (Blueprint $t) {
            $t->id();
            $t->string('nombre',120)->unique();
            $t->string('slug',140)->unique();
            $t->text('descripcion')->nullable();
            $t->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficios');
    }
};
