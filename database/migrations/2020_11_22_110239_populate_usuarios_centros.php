<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\User;
use App\UsuarioCentro;

class PopulateUsuariosCentros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $usuarios = User::all();
        foreach($usuarios as $usuario){
            if($usuario->centro_id != null){
                UsuarioCentro::create(["user_id" => $usuario->id,"centro_id" => $usuario->centro_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
