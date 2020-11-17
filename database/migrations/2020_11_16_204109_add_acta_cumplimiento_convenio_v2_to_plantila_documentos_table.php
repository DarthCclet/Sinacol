<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddActaCumplimientoConvenioV2ToPlantilaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('tipo_documentos')->where('id',13)->update(
            [
              'objetos'=>'1,5,2,3,4,6,8,9'
            ]
        );
        DB::table('plantilla_documentos')->where('nombre_plantilla','CONSTANCIA DE CUMPLIMIENTO DE CONVENIO')->update(
            [
            'plantilla_body'=>'<p>&nbsp;</p>
            <table style="border-collapse: collapse; width: 52.1683%; height: 71px;" border="1">
            <tbody>
            <tr>
            <td style="width: 75.9259%;"><strong>N&uacute;mero de identificaci&oacute;n &uacute;nico</strong></td>
            <td style="width: 48.2301%;"><strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong>&nbsp;</td>
            </tr>
            <tr>
            <td style="width: 75.9259%;" rowspan="2"><strong>Buz&oacute;n electr&oacute;nico</strong></td>
            <td style="width: 48.2301%;"><strong class="mceNonEditable" data-nombre="solicitante_correo_buzon">[SOLICITANTE_CORREO_BUZON]</strong>&nbsp;</td>
            </tr>
            <tr>
            <td style="width: 48.2301%;"><strong class="mceNonEditable" data-nombre="solicitado_correo_buzon">[SOLICITADO_CORREO_BUZON]</strong>&nbsp;</td>
            </tr>
            <tr>
            <td style="width: 75.9259%;"><strong>Centro Federal de Conciliaci&oacute;n y Registro Laboral</strong></td>
            <td style="width: 48.2301%;"><strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong>&nbsp;</td>
            </tr>
            <tr>
            <td style="width: 75.9259%;"><strong>Sala de conciliaci&oacute;n</strong></td>
            <td style="width: 48.2301%;"><strong class="mceNonEditable" data-nombre="sala_sala">[SALA_SALA]</strong>&nbsp;</td>
            </tr>
            </tbody>
            </table>
            <p style="text-align: center;">&nbsp;</p>
            <p style="text-align: center;"><strong>CONSTANCIA DE CUMPLIMIENTO DE CONVENIO</strong></p>
            <p style="line-height: 8pt;"><strong><span style="font-family: Montserrat, sans-serif;">Solicitante: <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong>&nbsp; </span></strong></p>
            <p style="line-height: 8pt;"><strong><span style="font-family: Montserrat, sans-serif;">Citado(a): <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong>&nbsp; </span></strong></p>
            <p style="line-height: 8pt;"><strong><span style="font-family: Montserrat, sans-serif;">Funcionario(a) conciliador responsable: <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp; </span></strong></p>
            <p style="line-height: 8pt;"><strong><span style="font-family: Montserrat, sans-serif;">Objeto de la conciliaci&oacute;n: <strong class="mceNonEditable" data-nombre="solicitud_objeto_solicitudes">[SOLICITUD_OBJETO_SOLICITUDES]</strong>&nbsp; </span></strong></p>
            <p style="line-height: 8pt;"><strong><span style="font-family: Montserrat, sans-serif;"> Fecha y hora de audiencia: <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong>&nbsp; <strong class="mceNonEditable" data-nombre="audiencia_hora_inicio">[AUDIENCIA_HORA_INICIO]</strong>&nbsp; </span></strong></p>
            <p style="line-height: 8pt;"><strong><span style="font-family: Montserrat, sans-serif;">Asistencia de los interesados: Si.</span></strong></p>
            <p style="line-height: 8pt;"><strong><span style="font-family: Montserrat, sans-serif;">Fecha del conflicto: <strong class="mceNonEditable" data-nombre="solicitud_fecha_conflicto">[SOLICITUD_FECHA_CONFLICTO]</strong></span></strong></p>
            <p style="line-height: 8pt;"><strong><span style="font-family: Montserrat, sans-serif;">Posible prescripci&oacute;n de derechos: <strong class="mceNonEditable" data-nombre="solicitud_prescripcion">[SOLICITUD_PRESCRIPCION]</strong>&nbsp; </span></strong></p>
            <p style="line-height: 8pt;"><strong><span style="font-family: Montserrat, sans-serif;">Convenio conciliatorio: Si.</span></strong></p>
            <p>&nbsp;</p>
            <p style="text-align: justify;"><strong>Fundamentaci&oacute;n:</strong> Art&iacute;culos 684-E, fracci&oacute;n XIII, y 684-F, fracci&oacute;n VII, de la Ley Federal del Trabajo y art&iacute;culos 5 y 9, fracci&oacute;n I de la Ley Org&aacute;nica del Centro Federal de Conciliaci&oacute;n y Registro Laboral<strong>. </strong></p>
            <p style="text-align: justify;"><strong>Motivaci&oacute;n: </strong>De conformidad a la cl&aacute;usula D&eacute;cima del Convenio de Conciliaci&oacute;n suscrito entre el solicitante y citado, previamente referidos, y al efectuarse el pago en las condiciones convenidas en presencia del suscrito, se emite Constancia de Cumplimiento de Convenio. <strong>Doy fe.</strong></p>
            <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="conciliador_qr_firma">[CONCILIADOR_QR_FIRMA]</strong>&nbsp;</p>
            <div id="contenedor-firma" class="mceNonEditable" style="text-align: center; border-bottom: thin solid black; width: 40%; margin: 0 auto;">
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
            <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp;</p>
            <p>&nbsp;</p>'
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plantila_documentos', function (Blueprint $table) {
            //
        });
    }
}
