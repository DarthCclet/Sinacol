<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemuneracionToDatosLaboralesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datos_laborales', function (Blueprint $table) {
            $table->dropColumn('percepcion_mensual_neta');
            $table->dropColumn('percepcion_mensual_bruta');
            $table->dropColumn('no_afore');
            $table->decimal('remuneracion', 10, 2)->comment('Monto de percepción/pago');
            $table->string('nombre_jefe_directo')->nullable()->change();
            $table->string('nss')->nullable()->change();
            $table->string('no_issste')->nullable()->change();
            $table->date('fecha_salida', 0)->nullable()->change();
            $table->unsignedBigInteger('periodicidad_id')->nullable()->comment('FK de la tabla periodicidades');
            $table->foreign('periodicidad_id')->references('id')->on('periodicidades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datos_laborales', function (Blueprint $table) {
            $table->decimal('percepcion_mensual_neta', 10, 2)->comment('Monto de percepción mensual neta');
            $table->decimal('percepcion_mensual_bruta', 10, 2)->comment('Monto de percepción mensual bruta');
            $table->dropColumn('remuneracion');
            $table->dropForeign('periodicidad_id');
            $table->dropColumn('periodicidad_id');
        });
    }
}
