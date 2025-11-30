<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comentario_imagenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comentario_id');
            $table->string('ruta');   // ruta relativa en el disco (public)
            $table->unsignedTinyInteger('orden')->default(1); // ordenar 1,2,3
            $table->timestamps();

            $table->foreign('comentario_id')
                ->references('id')->on('comentarios')
                ->onDelete('cascade'); // si se borra el comentario, se borran los registros
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentario_imagenes');
    }
};
