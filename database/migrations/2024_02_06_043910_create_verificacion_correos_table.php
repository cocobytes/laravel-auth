<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificacionCorreosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verificacion_correos', function (Blueprint $table) {
            $table->string('id');
            $table->char('usuario_uuid');
            $table->string('correo_electronico', 100);
            $table->boolean('revocado')->default(0);
            $table->timestamps();

            $table->foreign('usuario_uuid')->references('uuid')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verificacion_correos');
    }
}
