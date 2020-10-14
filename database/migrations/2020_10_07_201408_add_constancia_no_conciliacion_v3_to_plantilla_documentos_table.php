<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConstanciaNoConciliacionV3ToPlantillaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('plantilla_documentos')->where('nombre_plantilla','CONSTANCIA DE NO CONCILIACIÓN')->update(
        [
        'plantilla_body'=>'<p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>CONSTANCIA DE NO CONCILIACI&Oacute;N</strong></span></p>
            <table style="border-collapse: collapse; width: 48.5601%; height: 128px; border-color: #7E8C8D; border-style: solid;" border="1">
            <tbody>
            <tr style="height: 21px;">
            <td style="width: 96.1515%; line-height: 8pt; height: 14px;  text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">N&uacute;mero de identificaci&oacute;n &uacute;nico</span></td>
            <td style="width: 51.4563%; line-height: 8pt; height: 14px;  text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><span style="font-family: Montserrat, sans-serif; font-size: 8pt;"><strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong>&nbsp; &nbsp;</span></span></td>
            </tr>
            <tr style="height: 21px; text-align: center;">
            <td style="width: 96.1515%; line-height: 8pt; height: 42px; text-align: center;" rowspan="2"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Buz&oacute;n electr&oacute;nico</span></td>
            <td style="width: 51.4563%; line-height: 8pt; height: 21px; text-align: center;"><strong class="mceNonEditable" data-nombre="solicitante_correo_buzon">[SOLICITANTE_CORREO_BUZON]</strong></td>
            </tr>
            <tr style="height: 21px; text-align: center;">
            <td style="width: 51.4563%; line-height: 8pt; height: 14px; text-align: center;"><strong class="mceNonEditable" data-nombre="solicitado_correo_buzon">[SOLICITADO_CORREO_BUZON]</strong></td>
            </tr>
            <tr style="height: 21px;">
            <td style="width: 96.1515%; line-height: 8pt; height: 14px; text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">N&uacute;mero de solicitantes</span></td>
            <td style="width: 51.4563%; line-height: 8pt; height: 14px; text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp; &nbsp; &nbsp; &nbsp; <strong class="mceNonEditable" data-nombre="solicitud_total_solicitantes">[SOLICITUD_TOTAL_SOLICITANTES]</strong>&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;</span><strong class="mceNonEditable" data-nombre="centro_nombre">&nbsp;</strong></td>
            </tr>
            <tr style="height: 13px;">
            <td style="width: 96.1515%; line-height: 8pt; height: 14px; text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">N&uacute;mero de citados</span></td>
            <td style="width: 51.4563%; line-height: 8pt; height: 14px; text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><span style="font-size: 8pt;">&nbsp; &nbsp; <strong class="mceNonEditable" data-nombre="solicitud_total_solicitados">[SOLICITUD_TOTAL_SOLICITADOS]</strong>&nbsp; </span>&nbsp; &nbsp;</span></td>
            </tr>
            <tr style="height: 13px;">
            <td style="width: 96.1515%; line-height: 8pt; height: 14px; text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Secuencia de constancia</span></td>
            <td style="width: 51.4563%; line-height: 8pt; height: 14px; text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12t;">1</span></td>
            </tr>
            <tr style="height: 21px; text-align: center;">
            <td style="width: 96.1515%; line-height: 8pt; height: 14px; text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Centro</span></td>
            <td style="width: 51.4563%; line-height: 8pt; height: 14px; text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong>&nbsp; </span></td>
            </tr>
            </tbody>
            </table>      
              <p style="line-height: 8pt;">&nbsp;</p>
              <p style="line-height: 6pt;">&nbsp;</p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Trabajador(a): <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong>&nbsp; </strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Empleador(a): <strong class="mceNonEditable" data-nombre="solicitado_nombre_completo">[SOLICITADO_NOMBRE_COMPLETO]</strong>&nbsp; &nbsp;</strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Funcionario(a) conciliador responsable: &nbsp; <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp; &nbsp;</strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Objeto de la conciliaci&oacute;n: <strong class="mceNonEditable" data-nombre="solicitud_objeto_solicitudes">[SOLICITUD_OBJETO_SOLICITUDES]</strong>&nbsp; </strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Fecha y hora de audiencia: <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong> <strong class="mceNonEditable" data-nombre="audiencia_hora_inicio">[AUDIENCIA_HORA_INICIO]</strong>&nbsp; </strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Asistencia de los interesados: Si</strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Fecha de ratificacion de la solicitud: <strong class="mceNonEditable" data-nombre="solicitud_fecha_ratificacion">[SOLICITUD_FECHA_RATIFICACION]</strong>&nbsp; </strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Fecha de conflicto: <strong class="mceNonEditable" data-nombre="solicitud_fecha_conflicto">[SOLICITUD_FECHA_CONFLICTO]</strong>&nbsp; </strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Posible prescripci&oacute;n de derechos: <strong class="mceNonEditable" data-nombre="solicitud_prescripcion">[SOLICITUD_PRESCRIPCION]</strong></strong></span></p>
              <p style="line-height: 8pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Nueva fecha de conciliaci&oacute;n: <strong class="mceNonEditable" data-nombre="audiencia_reprogramada">[AUDIENCIA_REPROGRAMADA]</strong>&nbsp; </strong></span></p>
              <p style="text-align: justify; line-height: 11pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Fundamentaci&oacute;n:</strong> Art&iacute;culos 684-E, fracci&oacute;n VIII, tercer p&aacute;rrafo y 684-F, fracci&oacute;n VIII, de la Ley Federal del Trabajo y art&iacute;culos 5 y 9, fracci&oacute;n I y VIII de la Ley Org&aacute;nica del Centro Federal de Conciliaci&oacute;n y Registro Laboral.</span></p>
              <p style="line-height: 11pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Motivaci&oacute;n:&nbsp;</strong>Falta de acuerdo entre los interesados para negociar las prestaciones que derivan de la recisi&oacute;n del contrato de trabajo</span></p>
              <p style="text-align: justify; line-height: 11pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Se dejan a salvo los derechos de las partes para solicitar una nueva fecha de audiencia en t&eacute;rminos del art&iacute;culo 684-E, fracci&oacute;n VIII, &uacute;ltimo p&aacute;rrafo.&nbsp;</span></p>
              <p style="text-align: justify; line-height: 11pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">De conformidad con los principios constitucionales de legalidad, imparcialidad, confiabilidad, eficacia, objetividad, profesionalismo, transparencia y publicidad, se expide la&nbsp; <strong>CONSTANCIA DE NO CONCILIACI&Oacute;N</strong>.</span></p>
              <p style="text-align: justify; line-height: 11pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Finalmente, se dejan a salvo los derechos de los interesados para ejercer las acciones respectivas ante el Tribunal laboral competente, en t&eacute;rminos de los art&iacute;culos 123, apartado A, fracci&oacute;n XX, de la Constituci&oacute;n Pol&iacute;tica de los Estados Unidos Mexicanos; 521, fracci&oacute;n III, 870 Bis, de la Ley Federal del Trabajo. <strong>Doy fe.&nbsp;</strong></span></p>
              <div id="contenedor-firma" class="mceNonEditable" style="text-align: center; border-bottom: thin solid black; width: 40%; margin: 0 auto;">
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
              <p style="text-align: center; line-height: 8pt;"><strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp;</p>
              <p style="text-align: center; line-height: 6pt;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>FUNCIONARIO CONCILIADOR.</strong></span></p>',
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
        Schema::table('plantilla_documentos', function (Blueprint $table) {
            //
        });
    }
}
