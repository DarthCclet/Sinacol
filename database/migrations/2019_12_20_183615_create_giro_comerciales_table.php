<?php

use App\Parsers\GiroComercialParser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

class CreateGiroComercialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('giro_comerciales', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK del catálogo de giro comercial');
            $table->string('codigo')->comment('Código del catálogo SCIAN del INEGI');
            $table->string('nombre')->comment('Nombre del giro comercial');
            $table->integer('ambito_id')->default('3')->nullable()->comment('Fk de la tabla ambitos');
            $table->foreign('ambito_id')->references('id')->on('ambitos');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            NestedSet::columns($table);
            $table->timestamps();
        });
        $tabla_nombre = 'giro_comerciales';
        $comentario_tabla = 'Tabla donde se almacena el catálogo de giros comerciales para empresas.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

        $pathArchivo = base_path('database/datafiles/GirosComercialesSIAN.xlsx');
        $giroComercialParser = new GiroComercialParser();
        $giroComercialParser->parse($pathArchivo);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('giro_comerciales');
    }
}
