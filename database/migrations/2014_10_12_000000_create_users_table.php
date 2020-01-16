<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Llave primaria de usuarios');
            $table->string('name')->comment('Nombre de usuario');
            $table->string('email')->unique()->comment('Email del usuario. Es único');
            $table->timestamp('email_verified_at')->nullable()
                ->comment('Fecha y hora de envío de email de verificación de cuenta');
            $table->string('password',120)->comment('Clave de acceso del usuario como Hash Bcrypt');
            $table->rememberToken()->comment('Token que sirve para mantener sesión en sistema.');
            $table->softDeletes()->comment('Indica la hora y fecha que se ha realizado un borrado lógico del registro');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
