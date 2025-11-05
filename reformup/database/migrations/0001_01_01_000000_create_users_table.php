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
        // Tabla usuarios (dejamos users para laravel compatibilidad)
        Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 120);
        $table->string('apellidos', 120);
        $table->string('email', 190)->unique();
        $table->string('password');
        $table->string('telefono', 30)->nullable();
        $table->string('ciudad', 120)->nullable();
        $table->string('provincia', 120)->nullable();
        $table->string('cp', 10)->nullable();
        $table->string('direccion', 255)->nullable();
        $table->string('avatar', 255)->nullable();
        $table->rememberToken();
        $table->timestamps();
        
        //Indices para búsquedas rápidas
        $table->index(['nombre', 'apellidos']);
        $table->index('ciudad');
        $table->index('provincia');
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
