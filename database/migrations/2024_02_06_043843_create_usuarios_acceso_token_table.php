<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosAccesoTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_acceso_token', function (Blueprint $table) {
            $table->string('id');
            $table->char('usuario_uuid');
            $table->text('scoopes')->default('[]');
            $table->boolean('revocado')->default(0);
            $table->timestamp('expires_at');
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
        Schema::dropIfExists('usuarios_acceso_token');
    }
}
