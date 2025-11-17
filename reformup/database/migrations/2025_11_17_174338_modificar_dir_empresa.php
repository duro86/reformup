<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->renameColumn('dir_empresa', 'dir_cliente');
        });
    }

    public function down()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->renameColumn('dir_cliente', 'dir_empresa');
        });
    }
};
