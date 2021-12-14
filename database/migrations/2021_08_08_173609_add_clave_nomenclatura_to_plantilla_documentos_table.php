<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClaveNomenclaturaToPlantillaDocumentosTable extends Migration
{

    /*
    CGCI-1 Acuse de Solicitud ✔

    Identificación oficial trabajador

    CGCI-3 Citatorio de Conciliación ✔

    CGCI-4 Cédula de Notificación- Citatorio. SIGNO
    CGCI-4.1 Razón de Notificación del Citatorio de Conciliación. SIGNO

    Identificación oficial patrón (apoderado legal)
    Instrumento notarial

    CGCI-5 Acta de Audiencia de Conciliación ✔
    CGCI-5.5 Convenio de conflicto individual jurídico ✔
    CGCI-8 Constancia de Cumplimiento de Convenio ✔

    Documentos Adicionales:
    CGCI-5.2 Convenio de Reinstalación. ✔
    CGCI-5.3 Convenio de Terminación de Relación de Trabajo. ?
    CGCI-5.4 Convenio de Terminación con Múltiples Citados. ?

    CGCI-6 Acta de Multa. ✔
    CGCI-6.1 Cédula de Notificación de Multa.  SIGNO
    CGCI-6.2 Razón de Notificación del Acuerdo de Multa. SIGNO

    CGCI-9 Acta de No Comparecencia en Fecha de Pago. ?
    CGCI-10 Oficio Especial. (poner en funcion de oficio especial)

    No Conciliación

    CGCI-3 Citatorio de Conciliación ✔
    CGCI-4 Cédula de Notificación- Citatorio. SIGNO
    CGCI-4.1 Razón de Notificación del Citatorio de Conciliación. SIGNO

    CGCI-5 Acta de Audiencia de Conciliación ✔
    CGCI-5.1 Constancia de No Conciliación ✔

    Documentos Adicionales:
    CGCI-5.2 Convenio de Reinstalación. ✔
    CGCI-5.3 Convenio de Terminación de Relación de Trabajo. ?
    CGCI-5.4 Convenio de Terminación con Múltiples Citados. ?
    CGCI-6 Acta de Multa. ✔
    CGCI-6.1 Cédula de Notificación de Multa. SIGNO
    CGCI-6.2 Razón de Notificación del Acuerdo de Multa. SIGNO
    CGCI-9 Acta de No Comparecencia en Fecha de Pago. ?
    CGCI-10 Oficio Especial
    */


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plantilla_documentos', function (Blueprint $table) {
            $table->string('clave_nomenclatura')
                ->nullable()
                ->comment('Clave de la nomenclatura para la clasificación de documentos.')
            ;
        });

        $claves = [
            6  => 'CGCI-1',
            10 => 'CGCI-2',
            4  => 'CGCI-3',
            3  => 'CGCI-5',
            1  => 'CGCI-5.1',
            9  => 'CGCI-5.2',
            2  => 'CGCI-5.5',
            7  => 'CGCI-6',
            8  => 'CGCI-7',
            12 => 'CGCI-8',
        ];

        foreach($claves as $id => $clave) {
            $plantillaDocumento = \App\PlantillaDocumento::find($id);
            $plantillaDocumento->clave_nomenclatura = $clave;
            $plantillaDocumento->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plantilla_documentos', function (Blueprint $table) {
            $table->dropColumn('clave_nomenclatura');
        });
    }
}
