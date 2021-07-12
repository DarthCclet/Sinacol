<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHomeToRolesTable extends Migration
{
    /**
     * Run the migrations.
     * Agrega una columna "home" que almacena el URL del home default que le va a aparecer al usuario que contenga el rol.
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('home')->nullable()->comment('Indica el URL del home default para el rol');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('home');
        });
    }
}
