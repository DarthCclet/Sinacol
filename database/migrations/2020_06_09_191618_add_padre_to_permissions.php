<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPadreToPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->integer('padre_id')->nullable();
            $table->string('ruta')->nullable();
        });
        // insertamos los permisos
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/permissions.json"));
        DB::table('permissions')->truncate();
        foreach ($json as $permiso){
            DB::table('permissions')->insert(
                [
                    'id' => $permiso->id,
                    'name' => $permiso->name,
                    'description' => $permiso->description,
                    'ruta' => $permiso->ruta,
                    'padre_id' => $permiso->padre_id,
                    'guard_name' => 'web',
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
                ]
            );
        }
        DB::statement('ALTER SEQUENCE permissions_id_seq RESTART WITH 25');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->truncate();
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('ruta');
            $table->dropColumn('padre_id');
        });
    }
}
