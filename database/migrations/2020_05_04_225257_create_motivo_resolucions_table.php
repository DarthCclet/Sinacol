<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotivoResolucionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motivo_archivados', function (Blueprint $table) {
            $table->integer('id')->primary()->comment('Llave primaria del registro');
            $table->string('descripcion')->comment('Descripcion del motivo');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/motivos_archivados.json"));
        
        $tabla_nombre = 'motivo_archivados';
        $comentario_tabla = 'Tabla donde se almacenan Los motivos por los cuales se archiva un expediente';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

        foreach ($json->datos as $motivos){
            DB::table('motivo_archivados')->insert(
                [
                    'id' => $motivos->id,
                    'descripcion' => $motivos->descripcion,
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
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
        Schema::dropIfExists('motivo_archivados');
    }
}
