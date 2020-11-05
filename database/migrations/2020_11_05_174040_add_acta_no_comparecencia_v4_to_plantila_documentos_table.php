<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddActaNoComparecenciaV4ToPlantilaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('plantilla_documentos')->where('nombre_plantilla','ACTA DE ARCHIVADO POR NO COMPARECENCIA')->update(
            [
              'plantilla_body'=>'<p style="text-align: justify;">&nbsp;</p>
              <p style="text-align: justify;"><span style="font-size: 12pt; font-family: Montserrat, sans-serif;"><strong>Centro Federal de Conciliaci&oacute;n y Registro Laboral</strong></span><br /><span style="font-size: 12pt; font-family: Montserrat, sans-serif;"><strong>Oficina Estatal de <strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong></strong></span><br /><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Asunto:</strong> Archivo de asunto por falta de inter&eacute;s</span><br /><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Solicitante:</strong> <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong>&nbsp; </span><br /><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>N&uacute;mero de identificaci&oacute;n &uacute;nico:</strong> <strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong>&nbsp; </span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">En <strong class="mceNonEditable" data-nombre="centro_domicilio_estado">[CENTRO_DOMICILIO_ESTADO]</strong> a <strong class="mceNonEditable" data-nombre="fecha_actual">[FECHA_ACTUAL]</strong>,</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>VISTO</strong> el estado que guarda el expediente identificado con el n&uacute;mero <strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong>&nbsp; relativo a la solicitud de conciliaci&oacute;n realizada por <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong>, por falta de inter&eacute;s se formula resoluci&oacute;n en atenci&oacute;n a los siguientes:</span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>RESULTANDOS</strong></span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Primero</strong>. El <strong class="mceNonEditable" data-nombre="solicitud_fecha_recepcion">[SOLICITUD_FECHA_RECEPCION]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">, <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> solicit&oacute; ante este Centro, iniciar con el procedimiento de conciliaci&oacute;n prejudicial con el(los) citados <strong class="mceNonEditable" data-nombre="solicitud_nombres_solicitados">[SOLICITUD_NOMBRES_SOLICITADOS]</strong>&nbsp; por objeto de <strong class="mceNonEditable" data-nombre="solicitud_objeto_solicitudes">[SOLICITUD_OBJETO_SOLICITUDES]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Segundo</strong>. El <strong class="mceNonEditable" data-nombre="solicitud_fecha_ratificacion">[SOLICITUD_FECHA_RATIFICACION]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">, el Centro Federal de Conciliaci&oacute;n y Registro Laboral, Oficina Estatal <strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong>&nbsp; admiti&oacute; la solicitud de conciliaci&oacute;n, se&ntilde;alando que la celebraci&oacute;n de la Audiencia de Conciliaci&oacute;n se realizar&iacute;a el <strong class="mceNonEditable" data-nombre="audiencia_fecha_audiencia">[AUDIENCIA_FECHA_AUDIENCIA]</strong> </span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">a las <strong class="mceNonEditable" data-nombre="audiencia_hora_inicio">[AUDIENCIA_HORA_INICIO]</strong> </span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">horas en la sala de audiencia <strong class="mceNonEditable" data-nombre="sala_nombre">[SALA_NOMBRE]</strong>, en las instalaciones de este Centro.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Tercero</strong>. El <strong class="mceNonEditable" data-nombre="solicitud_fecha_ratificacion">[SOLICITUD_FECHA_RATIFICACION]</strong>, se concluy&oacute; la notificaci&oacute;n personal de el(los) citado(s)</span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">.</span></p>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Cuarto</strong>. El d&iacute;a de la audiencia,</span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;<strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> no se present&oacute; en ning&uacute;n momento durante el tiempo que se ten&iacute;a programado para la audiencia.</span></p>
              <p style="text-align: justify;"><br /><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">En esas condiciones, este Centro expone los siguientes:</span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>CONSIDERANDOS</strong></span></p>
              <ol style="list-style-type: upper-roman; text-align: justify;">
              <li><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Esta Unidad es competente para conocer del presente asunto en t&eacute;rminos de lo dispuesto por los art&iacute;culos 123, apartado A, fracci&oacute;n XX, p&aacute;rrafos 3 y 4 de la Constituci&oacute;n Pol&iacute;tica de los Estados Unidos Mexicanos, 684-B y 684-D, y 684-E, fracci&oacute;n X y p&aacute;rrafo 2 de la Ley Federal de Trabajo (LFT) y 5 de la Ley Org&aacute;nica del Centro Federal de Conciliaci&oacute;n y Registro Laboral (LOCFCRL).</span></li>
              <li><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">La solicitud de conciliaci&oacute;n fue presentada y admitida de conformidad con lo establecido por los art&iacute;culos 684-C y 684 E, Fracciones I, II, III, IV y V de la LFT.</span></li>
              <li><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">De conformidad con lo establecido por el art&iacute;culo 684-E, Fr. IV, de la LFT se se&ntilde;al&oacute; fecha y hora de la audiencia al solicitante y se notific&oacute; de la misma al(a los) citado(s)</span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">.</span></li>
              <li><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">El s</span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">olicitante <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong> </span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> no acudi&oacute; en ning&uacute;n momento durante el tiempo que se ten&iacute;a programado para celebrar la audiencia, sin que se notificara a este Centro causa justificada para no hacerlo.</span></li>
              </ol>
              <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">Por lo expuesto, se:</span></p>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>RESUELVE</strong></span></p>
              <p style="text-align: justify;"><br /><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Primero</strong>. Se archiva el expediente <strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong> </span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> que consta desde el <strong class="mceNonEditable" data-nombre="solicitud_fecha_ratificacion">[SOLICITUD_FECHA_RATIFICACION]</strong></span><span style="font-family: Montserrat, sans-serif; font-size: 12pt;">&nbsp;en este Centro, por falta de inter&eacute;s del Solicitante.</span><br /><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Segundo</strong>. Se le informa que el plazo de prescripci&oacute;n se reanud&oacute; a partir del d&iacute;a siguiente en que fue programada la audiencia, de conformidad con el art&iacute;culo 684-E, Fr. X de la LFT.</span><br /><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Tercero</strong>. Conforme al art&iacute;culo 521 Fr. III, se dejan a salvo los derechos del trabajador para solicitar nuevamente la conciliaci&oacute;n y con ello interrumpir nuevamente la prescripci&oacute;n.</span><br /><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"><strong>Cuarto</strong>. La interrupci&oacute;n de la prescripci&oacute;n cesa al d&iacute;a siguiente en que se emite esta Resoluci&oacute;n, de conformidad con el art&iacute;culo 521, Fr III de la LFT.</span></p>
              <div id="contenedor-firma" class="mceNonEditable" style="text-align: justify; border-bottom: thin solid black; width: 40%; margin: 0px auto;">
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
              <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif; font-size: 12pt;"> <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp; </span></p>
              <p style="text-align: justify;">&nbsp;</p>'
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
