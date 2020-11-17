<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConstanciaIncompetenciaV3ToPlantillaDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('plantilla_documentos')->where('nombre_plantilla','CONSTANCIA DE INCOMPETENCIA')->update(
            [
            'plantilla_body'=>'<p>&nbsp;</p>
            <table style="border-collapse: collapse; width: 56.7393%; height: 72px;" border="1">
            <tbody>
            <tr style="height: 18px;">
            <td style="width: 50%; height: 18px;">N&uacute;mero de identificaci&oacute;n &uacute;nico</td>
            <td style="width: 50%; height: 18px;"><strong class="mceNonEditable" data-nombre="expediente_folio">[EXPEDIENTE_FOLIO]</strong>&nbsp;</td>
            </tr>
            <tr style="height: 18px;">
            <td style="width: 50%; height: 18px;">Buz&oacute;n electr&oacute;nico</td>
            <td style="width: 50%; height: 18px;"><strong class="mceNonEditable" data-nombre="solicitante_correo_buzon">[SOLICITANTE_CORREO_BUZON]</strong></td>
            </tr>
            <tr style="height: 18px;">
            <td style="width: 50%; height: 18px;">Centro Federal de Conciliaci&oacute;n y Registro Laboral</td>
            <td style="width: 50%; height: 18px;"><strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong>&nbsp;</td>
            </tr>
            <tr style="height: 18px;">
            <td style="width: 50%; height: 18px;">Sala de conciliaci&oacute;n</td>
            <td style="width: 50%; height: 18px;"><strong class="mceNonEditable" data-nombre="sala_nombre">[SALA_NOMBRE]</strong>&nbsp;</td>
            </tr>
            </tbody>
            </table>
            <p style="text-align: justify;">&nbsp;</p>
            <p style="text-align: center;"><span style="font-family: Montserrat, sans-serif;"><strong>CONSTANCIA DE INCOMPETENCIA</strong></span></p>
            <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif;"><strong>Solicitante: <strong class="mceNonEditable" data-nombre="solicitante_nombre_completo">[SOLICITANTE_NOMBRE_COMPLETO]</strong>&nbsp; <br />Citado: <strong class="mceNonEditable" data-nombre="solicitud_nombres_solicitados">[SOLICITUD_NOMBRES_SOLICITADOS]</strong><br />Funcionario(a) conciliador responsable: <strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp; <br />Objeto de la conciliaci&oacute;n: <strong class="mceNonEditable" data-nombre="solicitud_objeto_solicitudes">[SOLICITUD_OBJETO_SOLICITUDES]</strong>&nbsp; </strong><strong><br />Fecha del conflicto: <strong class="mceNonEditable" data-nombre="solicitud_fecha_conflicto">[SOLICITUD_FECHA_CONFLICTO]</strong>&nbsp; </strong><strong><br />Posible prescripci&oacute;n de derechos: <strong class="mceNonEditable" data-nombre="solicitud_prescripcion">[SOLICITUD_PRESCRIPCION]</strong>&nbsp; </strong></span></p>
            <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif;"><strong><br />Fundamentaci&oacute;n: </strong>Art&iacute;culos 684-E, fracci&oacute;n V de la Ley Federal del Trabajo y art&iacute;culos 5 y 9, fracci&oacute;n I y VIII de la Ley Org&aacute;nica del Centro Federal de Conciliaci&oacute;n y Registro Laboral.</span></p>
            <p style="text-align: justify;"><span style="font-family: Montserrat, sans-serif;"><strong>Motivaci&oacute;n: </strong>La Oficina Regional del Centro Federal de Conciliaci&oacute;n y Registro <strong class="mceNonEditable" data-nombre="centro_nombre">[CENTRO_NOMBRE]</strong> de conformidad con los elementos presentados, resulta ser incompetente por jurisdicci&oacute;n, por lo que se remite la solicitud con n&uacute;mero de identificaci&oacute;n <strong class="mceNonEditable" data-nombre="centro_abreviatura">[CENTRO_ABREVIATURA]</strong>/<strong class="mceNonEditable" data-nombre="solicitud_folio">[SOLICITUD_FOLIO]</strong>/<strong class="mceNonEditable" data-nombre="solicitud_anio">[SOLICITUD_ANIO]</strong> al Centro de Conciliaci&oacute;n de <strong class="mceNonEditable" data-nombre="centro_domicilio_estado">[CENTRO_DOMICILIO_ESTADO]</strong>.<br />De conformidad con los principios constitucionales de legalidad, imparcialidad, confiabilidad, eficacia, objetividad, profesionalismo, transparencia y publicidad, se expide notifica a las partes de la incompetencia para que el desahogo de la conciliaci&oacute;n se realice ante el Centro de Conciliaci&oacute;n de <strong class="mceNonEditable" data-nombre="centro_domicilio_estado">[CENTRO_DOMICILIO_ESTADO]</strong><strong>.</strong></span></p>
            <p style="text-align: justify;">Finalmente, se dejan a salvo los derechos de los interesados para ejercer las acciones respectivas ante el Tribunal laboral competente, en t&eacute;rminos de los art&iacute;culos 123, apartado A, fracci&oacute;n XX, de la Constituci&oacute;n Pol&iacute;tica de los Estados Unidos Mexicanos; 521, fracci&oacute;n III, 870 Bis, de la Ley Federal del Trabajo. <strong>Doy fe.</strong></p>
            <p style="text-align: center;"><strong><strong class="mceNonEditable" data-nombre="conciliador_qr_firma">[CONCILIADOR_QR_FIRMA]</strong>&nbsp; </strong></p>
            <div id="contenedor-firma" class="mceNonEditable" style="text-align: center; border-bottom: thin solid black; width: 40%; margin: 0 auto;">
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <strong id="espacio-firma">[ESPACIO_FIRMA]</strong></div>
            <p style="text-align: center;"><strong class="mceNonEditable" data-nombre="conciliador_nombre_completo">[CONCILIADOR_NOMBRE_COMPLETO]</strong>&nbsp;</p>'
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
