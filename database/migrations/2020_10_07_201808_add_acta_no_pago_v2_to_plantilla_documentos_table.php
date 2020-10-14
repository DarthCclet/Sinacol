<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddActaNoPagoV2ToPlantillaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('plantilla_documentos')->where('nombre_plantilla','ACTA DE NO COMPARECENCIA EN FECHA DE PAGO')->update(
            [
            'nombre_plantilla'=> 'ACTA DE NO COMPARECENCIA EN FECHA DE PAGO',
            'plantilla_body'=>'<p>&nbsp;</p>
                <table style="border-collapse: collapse; width: 51.9909%; height: 49px;" border="1">
                <tbody>
                <tr>
                <td style="width: 50%; font-family: Montserrat, sans-serif; font-size: 12pt;">N&uacute;mero de identificaci&oacute;n &uacute;nico</td>
                <td style="width: 50%; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong>&nbsp;</td>
                </tr>
                <tr>
                <td style="width: 50%; font-family: Montserrat, sans-serif; font-size: 12pt;">Buz&oacute;n electr&oacute;nico</td>
                <td style="width: 50%; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="solicitante_correo_buzon">[SOLICITANTE_CORREO_BUZON]</strong>&nbsp;</td>
                </tr>
                <tr>
                <td style="width: 50%; font-family: Montserrat, sans-serif; font-size: 12pt;">Centro de conciliaci&oacute;n</td>
                <td style="width: 50%; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong>&nbsp;</td>
                </tr>
                </tbody>
                </table>
                <p style="text-align: center; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>CONSTANCIA DE NO PAGO PARCIAL</strong></p>
                <p style="line-height: 8pt; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Solicitante</strong>: <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong>&nbsp;</p>
                <p style="line-height: 8pt; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Citado</strong>(a): <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong>&nbsp;</p>
                <p style="line-height: 8pt; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Funcionario(a) conciliador responsable:</strong> <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp;</p>
                <p style="line-height: 8pt; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Objeto de la conciliaci&oacute;n:</strong> <strong class="mceNonEditable" data-nombre="solicitud_objeto_solicitudes">[SOLICITUD_OBJETO_SOLICITUDES]</strong></p>
                <p style="line-height: 8pt; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Fecha y hora de audiencia:</strong>&nbsp; <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong>&nbsp; <strong class="mceNonEditable" data-nombre="audiencia_hora_inicio">[AUDIENCIA_HORA_INICIO]</strong>&nbsp; .</p>
                <p style="line-height: 8pt; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Asistencia de los interesados:</strong> S&iacute;</p>
                <p style="line-height: 8pt; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Fecha del conflicto:</strong> <strong class="mceNonEditable" data-nombre="solicitud_fecha_conflicto">[SOLICITUD_FECHA_CONFLICTO]</strong>&nbsp;</p>
                <p style="line-height: 8pt; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Posible prescripci&oacute;n de derechos:</strong> <strong class="mceNonEditable" data-nombre="solicitud_prescripcion">[SOLICITUD_PRESCRIPCION]</strong>&nbsp;</p>
                <p style="line-height: 8pt; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Convenio conciliatorio:</strong> Si.</p>
                <p style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Fundamentaci&oacute;n:</strong> Art&iacute;culos 684-E, fracci&oacute;n VIII, segundo p&aacute;rrafo; as&iacute; como pen&uacute;ltimo y &uacute;ltimo p&aacute;rrafo del citado art&iacute;culo y 684-F, fracci&oacute;n VIII, de la Ley Federal del Trabajo y art&iacute;culos 5 y 9, fracci&oacute;n I de la Ley Org&aacute;nica del Centro Federal de Conciliaci&oacute;n y Registro Laboral.</p>
                <p style="font-family: Montserrat, sans-serif; font-size: 12pt;">Motivaci&oacute;n: Falta de pago diferido pactado de conformidad a la Cl&aacute;usula Sexta del convenio de Conciliaci&oacute;n de <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong> .&nbsp;</p>
                <p style="font-family: Montserrat, sans-serif; font-size: 12pt;">Por lo que con base en la Cl&aacute;usula S&eacute;ptima del citado convenio se inicia la generaci&oacute;n del pago por pena convencional a raz&oacute;n del salario diario que percib&iacute;a dicha parte antes de finalizar la relaci&oacute;n de trabajo, por cada d&iacute;a que transcurra sin que se d&eacute; cumplimiento cabal al convenio.&nbsp;</p>
                <p style="font-family: Montserrat, sans-serif; font-size: 12pt;">De conformidad con los principios constitucionales de legalidad, imparcialidad, confiabilidad, eficacia, objetividad, profesionalismo, transparencia y publicidad, se expide la <strong>CONSTANCIA DE NO PAGO PARCIAL.</strong></p>
                <p style="font-family: Montserrat, sans-serif; font-size: 12pt;">Finalmente, se dejan a salvo los derechos de los interesados para ejercer las acciones respectivas ante el Tribunal laboral competente, en t&eacute;rminos de los art&iacute;culos 123, apartado A, fracci&oacute;n XX, de la Constituci&oacute;n Pol&iacute;tica de los Estados Unidos Mexicanos. <strong>Doy fe.&nbsp;</strong></p>
                <div id="contenedor-firma" class="mceNonEditable" style="text-align: center; border-bottom: thin solid black; width: 40%; margin: 0 auto;">
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
                <p style="text-align: center; font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp;</p>'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plantilla_documentos', function (Blueprint $table) {
            //
        });
    }
}
