<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToRoleAndPermition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/roles.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $rol){
            DB::table('roles')->insert(
                [
                    'name' => $rol->name,
                    'description' => $rol->description,
                    'guard_name' => 'web'
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
