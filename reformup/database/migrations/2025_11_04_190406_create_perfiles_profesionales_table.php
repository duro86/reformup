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
        //Tabla perfiles_profesionales(usuarios profesionales)
        Schema::create('perfiles_profesionales', function (Blueprint $table) {
            $table->id(); // auto_increment primary key
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('empresa',160);
            $table->string('cif',20)->unique();
            $table->string('email_empresa',190)->unique();
            $table->text('bio')->nullable();
            $table->string('web',190)->nullable();
            $table->string('telefono_empresa',30)->nullable();
            $table->string('ciudad',120)->nullable();
            $table->string('provincia',120)->nullable();
            $table->string('dir_empresa',255)->nullable();
            $table->decimal('puntuacion_media',3,2)->default(0.00);
            $table->unsignedInteger('trabajos_realizados')->default(0);
            $table->boolean('visible')->default(false);
            $table->string('avatar',255)->nullable();
            $table->timestamps();

            $table->softDeletes(); //Para “borrar” perfiles sin perderlos de verdad: se marca deleted_at y puedes restaurarlos

            $table->unique('user_id'); //Para forzar una relación 1:1: cada user puede tener como mucho un perfil_profesional

            //Indices para búsquedas rápidas
            $table->index('visible');
            $table->index('ciudad');
            $table->index('provincia');
            $table->index('puntuacion_media');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfiles_profesionales');
    }
};
